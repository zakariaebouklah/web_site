<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebSiteController extends AbstractController
{
    #[Route('/', name: 'app')]
    public function root(): Response{
        return $this->render('web_site/root.html.twig', [

        ]);
    }

    #[Route('/site/accueil', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('web_site/accueil.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/us', name: 'app_us')]
    public function us(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/us.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/formation_doctorale', name: 'app_formation_doctorale')]
    public function formation(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/formation.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }
}
