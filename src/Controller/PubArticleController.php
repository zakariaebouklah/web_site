<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Form\DeleteFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
#[Route('/pub')]
class PubArticleController extends AbstractController
{
    #[Route('/new_article', name: 'app_new_article')]
    #[Route('/edit_article/{slug}', name: 'app_edit_article')]
    public function formArticle(Request $request, EntityManagerInterface $manager, string $_route, Article $article = null): Response
    {
        if ($article === null)
        {
            if ($_route == "app_edit_article")
            {
                throw new NotFoundHttpException();
            }
            $article = new Article();
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($user);

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute("app_article", ['slug'=>$article->getSlug()]);
        }

        return $this->render('pub/article.html.twig', [
            'form'=> $form->createView(),
            'edit'=> $article->getTitle() !== null
        ]);
    }

    #[Route('/delete_article/{slug}', name: "app_delete_article")]
    public function deleteArticle(Article $article, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->remove($article);
            $em->flush();

            return $this->redirectToRoute("app_articles");
        }

        return $this->render("web_site/delete.html.twig", [
            'form'=> $form->createView()
        ]);

    }
}
