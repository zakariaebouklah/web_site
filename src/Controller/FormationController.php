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
    public function resultsFormation(ChartBuilderInterface $chartBuilder, SubscriptionRepository $repository, AnnonceFormationRepository $annonceFormationRepository): Response
    {
        /**
         * @var AnnonceFormation $activeFormation
         * @var AnnonceFormation $latestFormation
         */
        $activeFormation = $annonceFormationRepository->findCurrentFormation();
        $latestFormation = $annonceFormationRepository->findOneBy([], ['droppedAt'=>'DESC']);

        /**
         * @var array<Subscription> $subscriptions
         */
        $subscriptions = $repository->findBy(['annonceFormation'=>$latestFormation], ['createdAt'=>'DESC']);
//        dd($subscriptions);

        /**
         * getting the institutions stats.
         */
//        $FSJES = $repository->findInstitutionsStats($latestFormation, "Faculté des Sciences Juridiques, Economiques et Sociales d'Oujda");
//        $ENCG = $repository->findInstitutionsStats($latestFormation, "Ecole Nationale de Commerce et de Gestion d'Oujda");
//        $EST = $repository->findInstitutionsStats($latestFormation, "Ecole Supérieure de Technologie d'Oujda");
//        $FPN = $repository->findInstitutionsStats($latestFormation, "Faculté Pluridisciplinaire de Nador");
//        $Other = $repository->findInstitutionsStats($latestFormation, "Autres...");

        dd($repository->findInstitutions($latestFormation));

        /**
         * getting the ateliers stats.
         */

//        $DI = $repository->findAtelierStats($latestFormation, "");

        $chartForInstitution = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartForInstitution->setData([
            'labels'=>[
                'Faculté des Sciences Juridiques, Economiques et Sociales d\'Oujda',
                'Ecole Nationale de Commerce et de Gestion d\'Oujda',
                'Ecole Supérieure de Technologie d\'Oujda',
                'Faculté Pluridisciplinaire de Nador',
                'Autres...',
                ],
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
                    'data' => [count($FSJES), count($ENCG), count($EST), count($FPN), count($Other)],
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
            'labels'=>[
                'Première année',
                'Deuxième année',
                'Troisième année',
                'Quatrième année',
                'Cinquième année',
                'Sixième année'
            ],
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
                    'data' => [15, 10, 5, 2, 20, 30],
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

        $chartForLabos = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartForLabos->setData([
            'labels'=>[
                'Communication éducation linguistique et Humanités numériques',
                'GRET',
                'Interdisciplinaire de Recherche Economiques, Économétriques et Managériales',
                'LARGAIM',
                'LERITEDA',
                'LIRAM',
                'LURIGOR',
                'LURICOR',
                'MARSAG ENCGT',
                'MADEO',
                'Économie sociale et solidaire et développement local',
            ],
            'datasets'=>[
                [
                    'backgroundColor' => [
                        'rgb(255, 185, 49)'
                    ],
                    'borderColor' => 'rgb(0, 0, 0)',
                    'data' => [15, 10, 5, 2, 20, 30, 12, 6, 5, 18, 7],
                ]
            ]
        ]);
        $chartForLabos->setOptions([
            'indexAxis'=>'y',
            'plugins'=> [
                'legend'=>[
                    'display'=> false
                ]
            ]
        ]);

        /*--------------------------------------------------------------------------------*/

        $chartForAteliers = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chartForAteliers->setData([
            'labels'=>[
                'Le doctorat n’est pas un long fleuve tranquille (DEKHISSI Ilyass - EST - UMP Oujda)',
                'Développer son esprit scientifique par les 2C : Connaissance & Conscience  (MAJIDI Fouzia - FSJES - UMP Oujda)',
                'Interdisciplinaire de Recherche Economiques, Économétriques et Managériales',
                'La motivation dans la recherche scientifique (BERRICHI Abdelouahed - FSJES - UMP Oujda)',
                'Spécifier son objet de recherche (EL ATTAR Abdellilah - FSJES - UMP Oujda)',
                'Conduire son projet de recherche selon la perspective quantitative et qualitative (Karim BENNIS - FSJES - USMBA Fès)',
                'Design de recherche : construire son modèle de recherche dans une perspective hypothético-déductive (HAFIANE Mohammed Amine - FSJES - UMP Oujda)',
                'Réussir son étude empirique : De l’opérationnalisation à la modélisation par les équations structurelles et la vérification des hypothèses (EDDAOU Mohammed - FSJES- UMP Oujda)',
                'L’art de rédiger son article scientifique (FIKRI Khalid - FSJES - UMP Oujda)',
            ],
            'datasets'=>[
                [
                    'backgroundColor' => [
                        'rgb(255, 185, 49)'
                    ],
                    'borderColor' => 'rgb(0, 0, 0)',
                    'data' => [15, 10, 5, 2, 20, 30, 12, 6, 5],
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
            'chartForLabos'=>$chartForLabos,
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
            'form' => $form->createView()
        ]);
    }

}
