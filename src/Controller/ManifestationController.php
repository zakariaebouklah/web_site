<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ManifestationController extends AbstractController
{
    #[Route('/site/manifestation_science', name: 'app_manifestation')]
    public function manifestation(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/manifestation.html.twig');
    }

    #[Route('/site/events', name: 'app_events')]
    public function eventsList(EventRepository $eventRepository): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $events = $eventRepository->findBy([], ['droppedAt'=>'DESC']);

        return $this->render('manifestation/events.html.twig', [
            'events'=>$events
        ]);
    }
}
