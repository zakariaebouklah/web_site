<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\DeleteFormType;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/manifestation')]
class EventManipController extends AbstractController
{
    #[Route('/new_event', name: "app_new_event")]
    #[Route('/edit_event/{slug}', name: "app_edit_event")]
    public function eventForm(Request $request, EntityManagerInterface $manager, string $_route, SluggerInterface $slugger, Event $event = null): Response
    {
        if ($event === null)
        {
            if ($_route === "app_edit_event")
            {
                throw new NotFoundHttpException();
            }
            $event = new Event();
        }

        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event->setDroppedAt(new \DateTimeImmutable());

            $fiche = $form->get('fiche')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($fiche) {
                $originalFilename = pathinfo($fiche->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$fiche->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $fiche->move(
                        $this->getParameter('fiches_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setFiche($newFilename);
            }

            $programme = $form->get('programme')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($programme) {
                $originalFilename = pathinfo($programme->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$programme->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $programme->move(
                        $this->getParameter('programmes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $event->setProgramme($newFilename);
            }

            $manager->persist($event);
            $manager->flush();

            return $this->redirectToRoute("app_events");
        }

        return $this->render("manifestation/event.html.twig", [
            'form'=>$form->createView(),
            'edit'=>$event->getId() !== null
        ]);
    }

    #[Route('/delete_event/{slug}', name: "app_delete_event")]
    public function deleteEvent(Request $request, EntityManagerInterface $manager, Event $event): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->remove($event);
            $manager->flush();

            return $this->redirectToRoute("app_manifestation");
        }

        return $this->render("web_site/delete.html.twig", [
            'form'=>$form->createView()
        ]);
    }

}