<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Ouvrage;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use App\Form\DeleteFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
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
    #[Route('/', name: 'app_pub')]
    public function pub(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/pub.html.twig');
    }

    //Article Entity Manipulation
    #[Route('/articles', name: 'app_articles')]
    public function articles(ArticleRepository $repository): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $articles = $repository->findAll();

        return $this->render('pub/articles.html.twig', [
            'articles' => $articles,
        ]);
    }

    #[Route('/articles/{slug}', name: "app_article")]
    public function showArticle(Article $article, Request $request, EntityManagerInterface $manager): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $comment = new Comment();

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor($user);
            $comment->setArticle($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("app_articles");
        }

        return $this->render('pub/showArticle.html.twig', [
            'article' => $article,
            'form'=>$form->createView(),
            'username'=>$user
        ]);
    }

    #[Route('/edit_comment/{slug_comment}', name: "app_edit_comment")]
    public function editComment(Request $request, string $_route, EntityManagerInterface $manager, CommentRepository $commentRepository): Response
    {
        /**
         * @var object $comment
         */
        $comment = $commentRepository->findOneBy(['author'=>$this->getUser()]);

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($comment);
            $manager->flush();

//            return $this->redirectToRoute("app_article", [
//                'slug'=>$comment->getArticle()->getSlug()
//            ]);
            return $this->redirectToRoute("app_articles");
        }

        return $this->render("/pub/edit_comment.html.twig", [
            'form'=>$form->createView()
//            'slug_comment'=>$comment->getSlug()
        ]);
    }

    #[Route('/delete_comment/{slug}', name: "app_delete_comment")]
    public function deleteComment(Request $request, Comment $comment, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->remove($comment);
            $em->flush();

            return $this->redirectToRoute("app_articles");
        }

        return $this->render("web_site/delete.html.twig", [
            'form'=>$form->createView()
        ]);
    }

    //Ouvrage Entity Manipulation
    #[Route('/ouvrages', name: "app_ouvrages")]
    public function ouvrages(OuvrageRepository $repository): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $ouvrages = $repository->findAll();

        return $this->render('pub/ouvrages.html.twig', [
            'ouvrages' => $ouvrages
        ]);
    }

    #[Route('/ouvrages/{slug}', name: "app_ouvrage")]
    public function showOuvrage(Ouvrage $ouvrage): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('pub/showOuvrage.html.twig', [
            'ouvrage' => $ouvrage
        ]);
    }
}
