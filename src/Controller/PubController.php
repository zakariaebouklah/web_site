<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Ouvrage;
use App\Form\ArticleFormType;
use App\Form\DeleteFormType;
use App\Repository\ArticleRepository;
use App\Repository\OuvrageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pub")]
class PubController extends AbstractController
{
    //Article Entity Manipulation
    #[Route('/', name: 'app_pub')]
    public function pub(): Response
    {
        return $this->render('web_site/pub.html.twig');
    }

    #[Route('/articles', name: 'app_articles')]
    public function articles(ArticleRepository $repository): Response
    {
        $articles = $repository->findAll();

        return $this->render('pub/articles.html.twig', [
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{slug}', name: "app_article")]
    public function showArticle(Article $article): Response
    {
        return $this->render('pub/showArticle.html.twig', [
            'article' => $article
        ]);
    }

    //Ouvrage Entity Manipulation
    #[Route('/ouvrages', name: "app_ouvrages")]
    public function ouvrages(OuvrageRepository $repository): Response
    {
        $ouvrages = $repository->findAll();

        return $this->render('pub/ouvrages.html.twig', [
            'ouvrages' => $ouvrages
        ]);
    }

    #[Route('/ouvrages/{slug}', name: "app_ouvrage")]
    public function showOuvrage(Ouvrage $ouvrage): Response
    {
        return $this->render('pub/showOuvrage.html.twig', [
            'ouvrage' => $ouvrage
        ]);
    }
}
