<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\CommentForTopics;
use App\Entity\Topic;
use App\Entity\User;
use App\Form\CommentFormType;
use App\Form\CommentForTopicsFormType;
use App\Form\DeleteFormType;
use App\Form\TopicFormType;
use App\Repository\CommentForTopicsRepository;
use App\Repository\TopicRepository;
use App\Services\TopicCommentsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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

    #[Route('/related_topics', name: 'app_related_topics')]
    public function relatedThemes(): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        /**
         * @var User $user
         */

        $user = $this->getUser();
        $relatedTs = $user->getRelatedTopics();

        return $this->render('topic/related.html.twig', [
            'relatedTs'=>$relatedTs
        ]);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    #[Route('/topic/{slug}', name: "app_show_topic")]
    public function topic(Topic $topic): Response
    {
        $form = $this->createForm(CommentForTopicsFormType::class, null, [
            'topicSlug' => $topic->getSlug()
        ]);

        return $this->render("topic/showTopic.html.twig", [
            'topic'=>$topic,
            'all_comments'=>$topic->getComments(),
            'form'=>$form->createView()
        ]);
    }

    /**
     * @ParamConverter("topic", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("comment", options={"mapping": {"id": "id"}})
     */
    #[Route('/topic/{slug}/edit_comment/{id}/getForm', name: 'topic_edit_comment_form')]
    public function getEditCommentForm(Topic $topic, CommentForTopics $comment): Response
    {
        $form = $this->createForm(CommentForTopicsFormType::class, $comment, [
            'topicSlug' => $topic->getSlug()
        ]);

        return $this->render("topic/editTopicComment.html.twig", [
            'form'=>$form->createView()
        ]);
    }

    /**
     * @ParamConverter("topic", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("comment", options={"mapping": {"id": "id"}})
     */
    #[Route('/topic/{slug}/add_comment', name: 'app_topic_add_comment')]
    #[Route('/topic/{slug}/edit_comment/{id}', name: 'app_topic_edit_comment')]
    public function addTopicComment(
        Request $request,
        EntityManagerInterface $manager,
        Topic $topic,
        TopicCommentsService $service,
        string $_route,
        CommentForTopics $comment = null
    ): JsonResponse
    {
        if ($comment === null)
        {
            if ($_route === "topic_edit_comment")
            {
                throw new NotFoundHttpException();
            }
            $comment = new CommentForTopics();
        }
        $form = $this->createForm(CommentForTopicsFormType::class, $comment, [
            'topicSlug' => $topic->getSlug()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var ?User $user
             */
            $user = $this->getUser();

            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setOwner($user);
            $comment->setTopic($topic);

            $manager->persist($comment);
            $manager->flush();

            if ($_route === "app_topic_add_comment")
            {
                return $service->handleValidForm($request, $comment);
            } else
            {
                return $service->handleEditCommentForm($request, $comment, $form);
            }
        }
        return new JsonResponse($form->getErrors(), Response::HTTP_BAD_REQUEST);
    }

    #[Route('/delete_topic_comment/{id}', name: "app_delete_topic_comment", methods: ['GET'])]
    public function deleteComment(
        Request $request,
        EntityManagerInterface $manager,
        TopicCommentsService $service,
        CommentForTopics $comment
    ): JsonResponse
    {

        $manager->remove($comment);
        $manager->flush();

        return $service->handleDeleteComment($request);
    }

    /**
     * TODO: UPDATE COMMENT USING AJAX
     */
//    #[Route('/edit_topic_comment/{id}', name: "app_edit_topic_comment")]
//    #[Route('/topic/{slug}', name: "app_show_topic")]
//    public function editCommentProcess(TopicCommentsService $service, Request $request, CommentForTopics $comment, EntityManagerInterface $manager): Response
//    {
//
//        $form = $this->createForm(CommentForTopicsFormType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid())
//        {
//            $manager->persist($comment);
//            $manager->flush();
//
//            return $service->handleEditCommentForm($form);
//
//        }
//
//        return $this->render("topic/showTopic.html.twig", [
//            'form'=>$form->createView(),
//            'topic'=>null,
//            'all_comments'=>$comment->getTopic()->getComments(),
//        ]);
//    }

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

        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid())
        {
            $topic->setCreatedAt(new \DateTimeImmutable());
            $topic->setAuthor($user);

            $manager->persist($topic);
            $manager->flush();

            return $this->redirectToRoute("app_themes");
        }

        return $this->render("topic/topic.html.twig", [
            'form'=>$form->createView(),
            'edit'=>$topic->getId() !== null
        ]);
    }

    #[Route('/my_topics', name: 'app_my_themes')]
    public function myThemes(TopicRepository $topicRepository): Response
    {
        if ($this->getUser() === null)
        {
            return $this->redirectToRoute("app_accueil");
        }

        $myTopics = $topicRepository->findBy(['author'=>$this->getUser()], ['createdAt'=>'DESC']);

        return $this->render('topic/myTopics.html.twig', [
            'myTopics'=>$myTopics
        ]);
    }


    #[Route('/delete/{slug}', name: "app_delete_topic")]
    public function deleteTopic(Topic $topic, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(DeleteFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->remove($topic);
            $manager->flush();

            return $this->redirectToRoute("app_themes");
        }

        return $this->render("web_site/delete.html.twig", [
            'form'=>$form->createView()
        ]);
    }

//    #[Route('/delete_topic_comment/{id}', name: "app_delete_topic_comment")]
//    public function deleteComment(Request $request, EntityManagerInterface $manager, CommentForTopics $comment)
//    {
//        $form = $this->createForm(DeleteFormType::class);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid())
//        {
//            $manager->remove($comment);
//            $manager->flush();
//
//            /**
//             * TODO: DELETE COMMENT USING AJAX
//             */
//            return $this->redirectToRoute("app_themes");
//        }
//
//        return $this->render("web_site/delete.html.twig", [
//            'form'=>$form->createView(),
//            'id' =>$comment->getId()
//        ]);
//    }

//    #[Route('/edit_topic_comment/{id}', name: "app_edit_topic_comment")]
//    public function editComment(TopicCommentsService $service, Request $request, CommentForTopics $comment, EntityManagerInterface $manager): Response
//    {
//
//        $form = $this->createForm(CommentForTopicsFormType::class, $comment);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid())
//        {
//
//            $manager->persist($comment);
//            $manager->flush();
//
//            $service->handleEditCommentForm($form);
//
//            return $this->redirectToRoute("app_show_topic", [
//                'slug' => $comment->getTopic()->getSlug(),
//                'flash'=>$this->render("topic/flash_edit.html.twig")
//            ]);
//        }
//
//        return $this->render("topic/editTopicComment.html.twig", [
//            'form'=>$form->createView()
//        ]);
//    }
}
