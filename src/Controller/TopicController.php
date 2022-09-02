<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\TopicFormType;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/topics')]
class TopicController extends AbstractController
{
    #[Route('/', name: 'app_themes')]
    public function themes(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        return $this->render('web_site/themes.html.twig');
    }
    
    #[Route('/all', name: "app_all_topics")]
    public function allTopics(TopicRepository $topicRepository): Response
    {
        $topics = $topicRepository->findAll();

        return $this->render("topic/topics.html.twig", [
            'topics'=>$topics
        ]);
    }

    #[Route('/topic/{slug}', name: "app_show_topic")]
    public function topic(Request $request, EntityManagerInterface $manager, Topic $topic, Article $article = null): Response
    {
        $comment = new Comment();

        /**
         * @var ?User $user
         */
        $user = $this->getUser();

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setAuthor($user);
            $comment->setTopic($topic);
            $comment->setArticle($article);

            dd($article);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute("app_show_topic");
        }

        return $this->render("topic/showTopic.html.twig", [
            'topic'=>$topic,
            'form'=>$form->createView()
        ]);
    }

    #[Route('/new_topic', name: 'app_new_topic')]
    #[Route('/edit_topic/{slug}', name: 'app_edit_topic')]
    public function formTopic(Request $request, EntityManagerInterface $manager, string $_route, Topic $topic = null): Response
    {
        if ($topic === null)
        {
            if ($_route === "app_edit_topic")
            {
                throw new NotFoundHttpException();
            }
            $topic = new Topic();
        }

        $form = $this->createForm(TopicFormType::class, $topic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $topic->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute("app_themes");
        }

        return $this->render("topic/topic.html.twig", [
            'form'=>$form->createView(),
            'edit'=>$topic->getId() !== null
        ]);
    }
}
