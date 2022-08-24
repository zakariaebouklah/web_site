<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebSiteController extends AbstractController
{

    #[Route('/', name: 'app')]
    public function root(){
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
        return $this->render('web_site/us.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/pub_science', name: 'app_pub')]
    public function pub(): Response
    {
        return $this->render('web_site/pub.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/manifestation_science', name: 'app_manifestation')]
    public function manifestation(): Response
    {
        return $this->render('web_site/manifestation.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/formation_doctorale', name: 'app_formation_doctorale')]
    public function formation(): Response
    {
        return $this->render('web_site/formation.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }

    #[Route('/site/themes', name: 'app_themes')]
    public function themes(): Response
    {
        return $this->render('web_site/themes.html.twig', [
            'controller_name' => 'WebSiteController',
        ]);
    }
}
