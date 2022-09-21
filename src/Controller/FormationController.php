<?php

namespace App\Controller;

use App\Entity\AnnonceFormation;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\AnnonceFormType;
use App\Form\SubscriptionFormType;
use App\Repository\AnnonceFormationRepository;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/formations/formation_doctorale')]
class FormationController extends AbstractController
{
    #[Route('/', name: 'app_formation_doctorale')]
    public function formation(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/formation.html.twig');
    }

    #[Route('/current_formation', name: 'app_formation_courante')]
    public function currentFormation(AnnonceFormationRepository $repository): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $current = $repository->findCurrentFormation();

        return $this->render('formation/currentFormation.html.twig', [
            'current'=>$current
        ]);
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route('/inscription', name: "app_inscription")]
    public function inscriptionFormation(
        Request $request,
        EntityManagerInterface $manager,
        AnnonceFormationRepository $repository,
        SubscriptionRepository $subscriptionRepository
    ): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        /**
         * @var AnnonceFormation $activeFormation
         */
        $activeFormation = $repository->findCurrentFormation();

        /**
         * @var ?Subscription $subscription
         */
        $subscription = $subscriptionRepository->findOneBy(['subscriber'=>$user, 'annonceFormation'=>$activeFormation]);

        if ($subscription === null)
        {
            $subscription = new Subscription();

            $form = $this->createForm(SubscriptionFormType::class, $subscription);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $subscription->setCreatedAt(new \DateTimeImmutable());
                $subscription->setSubscriber($user);
                $subscription->setAnnonceFormation($activeFormation);

                $manager->persist($subscription);
                $manager->flush();

                return $this->redirectToRoute("app_formation_doctorale");
            }

            return $this->render('formation/inscription.html.twig', [
                'subscription'=>$subscription,
                'form'=>$form->createView(),
                'activeFormation'=>$activeFormation,
            ]);

        } else {
            return $this->render('formation/inscription.html.twig', [
                'subscription'=>$subscription,
                'form'=>null
            ]);
        }

    }

    #[Route('/archives', name: "app_archives_formations")]
    public function archives(AnnonceFormationRepository $repository): Response
    {

        $oldFormations = $repository->findAllOldEditions();

        return $this->render('formation/archives.html.twig', [
            'formations'=>$oldFormations
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    #[Route('/résultats_d\'inscription', name: "app_results")]
    public function resultsFormation(CacheInterface $cache, ChartBuilderInterface $chartBuilder, SubscriptionRepository $repository, AnnonceFormationRepository $annonceFormationRepository): Response
    {
        /**
         * @var AnnonceFormation $activeFormation
         */
        $activeFormation = $annonceFormationRepository->findCurrentFormation();

        /**
         * @var AnnonceFormation $latestFormation
         */
        $latestFormation = $annonceFormationRepository->findOneBy([], ['droppedAt'=>'DESC']);

        /**
         * @var array<Subscription> $subscriptions
         */
        $subscriptions = $repository->findBy(['annonceFormation'=>$latestFormation], ['createdAt'=>'DESC']);
//        dd($subscriptions);

        /**
         * getting the institutions stats.
         */

        $valuesForInstitution = $cache->get('institution_cache', function (ItemInterface $item) use ($repository, $latestFormation) {
            $item->expiresAfter(3600);

            $arr = $repository->findInstitutions($latestFormation);

            $institutions = [];
            $counts = [];

            foreach ($arr as $elm)
            {
                $institutions[] = $elm['homeInstitution'];
                $counts[] = $elm['count'];
            }

            return ['institutions' => $institutions, 'counts' => $counts];
        });

        /**
         * getting the ateliers stats.
         */

//        $cache->delete('atelier_cache');
        $valuesForAtelier = $cache->get('atelier_cache', function (ItemInterface $item) use ($repository, $latestFormation) {
            $item->expiresAfter(3600);

            $arr = $repository->findAteliers($latestFormation);

            $labels = [];
            $counts = [];

            foreach ($arr as $elm)
            {
                $labels[] = $elm['name'];
                $counts[] = $elm['count'];
            }

            return ['labels' => $labels, 'counts' => $counts];
        });

        /**
         * getting the years stats.
         */

        $valuesForYears = $cache->get('years_cache', function (ItemInterface $item) use ($repository, $latestFormation) {
            $item->expiresAfter(3600);

            $arr = $repository->findYears($latestFormation);

            $years = [];
            $counts = [];

            foreach ($arr as $elm)
            {
                $years[] = $elm['year'];
                $counts[] = $elm['count'];
            }

            return ['years' => $years, 'counts' => $counts];
        });

        /**
         * Charts...
         */

        $chartForInstitution = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartForInstitution->setData([
            'labels'=>$valuesForInstitution['institutions'],
            'datasets'=>[
                [
                    'backgroundColor' => [
                        'rgb(255, 185, 49)',
                        'rgb(103, 74, 64)',
                        'rgb(156, 195, 154)',
                        'rgb(204, 221, 239)',
                        'rgb(160, 123, 230)',
                    ],
                    'borderColor' => 'rgb(0, 0, 0)',
                    'data' => $valuesForInstitution['counts'],
                    'axis'=>'y',
                    'borderWidth'=>2
                ]
            ]
        ]);
        $chartForInstitution->setOptions([
            'indexAxis'=>'y',
            'plugins'=> [
                'legend'=>[
                    'display'=> false
                ]
            ]
        ]);

        /*--------------------------------------------------------------------------------*/

        $chartForYear = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chartForYear->setData([
            'labels'=>$valuesForYears['years'],
            'datasets'=>[
                [
                    'backgroundColor' => [
                        'rgb(255, 185, 49)',
                        'rgb(103, 74, 64)',
                        'rgb(156, 195, 154)',
                        'rgb(204, 221, 239)',
                        'rgb(160, 123, 230)',
                        'rgb(111, 29, 87)'
                    ],
                    'borderColor' => 'rgb(0, 0, 0)',
                    'data' => $valuesForYears['counts'],
                ]
            ]
        ]);

        $chartForYear->setOptions([
            'plugins'=> [
                'legend'=>[
                    'display'=> true
                ]
            ]
        ]);

        /*--------------------------------------------------------------------------------*/

//        $chartForLabos = $chartBuilder->createChart(Chart::TYPE_BAR);
//        $chartForLabos->setData([
//            'labels'=>[
//                'Communication éducation linguistique et Humanités numériques',
//                'GRET',
//                'Interdisciplinaire de Recherche Economiques, Économétriques et Managériales',
//                'LARGAIM',
//                'LERITEDA',
//                'LIRAM',
//                'LURIGOR',
//                'LURICOR',
//                'MARSAG ENCGT',
//                'MADEO',
//                'Économie sociale et solidaire et développement local',
//            ],
//            'datasets'=>[
//                [
//                    'backgroundColor' => [
//                        'rgb(255, 185, 49)'
//                    ],
//                    'borderColor' => 'rgb(0, 0, 0)',
//                    'data' => [15, 10, 5, 2, 20, 30, 12, 6, 5, 18, 7],
//                ]
//            ]
//        ]);
//        $chartForLabos->setOptions([
//            'indexAxis'=>'y',
//            'plugins'=> [
//                'legend'=>[
//                    'display'=> false
//                ]
//            ]
//        ]);

        /*--------------------------------------------------------------------------------*/



        $chartForAteliers = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartForAteliers->setData([
            'labels'=> $valuesForAtelier['labels'],
            'datasets'=>[
                [
                    'backgroundColor' => [
                        'rgb(255, 185, 49)'
                    ],
                    'borderColor' => 'rgb(0, 0, 0)',
                    'data' => $valuesForAtelier['counts'],
                ]
            ]
        ]);
        $chartForAteliers->setOptions([
            'indexAxis'=>'y',
            'plugins'=> [
                'legend'=>[
                    'display'=> false
                ]
            ]
        ]);

        /*--------------------------------------------------------------------------------*/

        return $this->render('formation/results.html.twig', [
            'subscriptions'=>$subscriptions,
            'activeFormation'=>$activeFormation,
            'chartForInstitution'=>$chartForInstitution,
            'chartForYear'=>$chartForYear,
            'chartForAteliers'=>$chartForAteliers
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/new_edition', name: 'app_new_edition')]
    public function newFormation(SluggerInterface $slugger, Request $request, EntityManagerInterface $manager): Response
    {
        $annonce = new AnnonceFormation();

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $card = $form->get('card')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($card) {
                $originalFilename = pathinfo($card->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$card->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $card->move(
                        $this->getParameter('cards_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $annonce->setCard($newFilename);
            }

            $annonce->setDroppedAt(new \DateTimeImmutable());
            $annonce->setStatus("ongoing");

            $manager->persist($annonce);
            $manager->flush();

            return $this->redirectToRoute("app_formation_courante");
        }

        return $this->render('formation/newFormationEdition.html.twig', [
            'form' => $form->createView(),
            'edit' => false
        ]);
    }

    /**
     * Cache Demo
     */
    #[Route('/cache', name: 'app_cache')]
    public function cacher(CacheInterface $cache): void
    {
        $value = $cache->get('yes', function (ItemInterface $item) {
            $item->expiresAfter(60);

            return rand(1, 100);
        });
        dd($value);
    }

}
