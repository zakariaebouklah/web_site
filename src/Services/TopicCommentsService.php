<?php

namespace App\Services;

use App\Entity\CommentForTopics;
use App\Form\CommentForTopicsFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Environment;

class TopicCommentsService
{
    public function __construct(
        private Environment $environment
    ) {}

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function handleValidForm(Request $request, CommentForTopics $comment): JsonResponse
    {
        /**
         * @var Session $session
         */
        $session = $request->getSession();
        $session->getFlashBag()->add(
          'success',
          'Votre commentaire a été ajouté.'
        );

        return new JsonResponse([
            'response'=> CommentForTopics::COMMENT_ADDED_SUCCESSFULLY,
            'html'=> $this->environment->render("topic/brandTopicComment.html.twig", [
                'comment' => $comment
            ]),
            'html_flash'=> $this->environment->render("topic/flash.html.twig")
        ]);
    }

    public function handleInValidForm(FormInterface $form): JsonResponse
    {
        return new JsonResponse([
            'response'=>CommentForTopics::COMMENT_NOT_ADDED,
        ]);
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function handleEditCommentForm(Request $request, CommentForTopics $comment, FormInterface $form): JsonResponse
    {
        /**
         * @var Session $session
         */
        $session = $request->getSession();
        $session->getFlashBag()->add(
            'notice',
            'Votre commentaire a été mis à jour. Actualisez La Page...'
        );

        return new JsonResponse([
            'response'=> CommentForTopics::COMMENT_UPDATED_SUCCESSFULLY,
            'html_flash'=>$this->environment->render("topic/flash_edit.html.twig")
        ]);
    }

    public function handleDeleteComment(Request $request): JsonResponse
    {
        $request->setSession(new Session());

        /**
         * @var Session $session
         */
        $session = $request->getSession();
        $session->getFlashBag()->add(
            'notice',
            'Votre commentaire a été supprimé. Actualisez La Page...'
        );

        return new JsonResponse([
            'response'=> CommentForTopics::COMMENT_DELETED_SUCCESSFULLY,
            'html_flash'=>$this->environment->render("topic/flash_edit.html.twig")
        ]);
    }
}
