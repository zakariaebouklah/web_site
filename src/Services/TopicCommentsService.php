<?php

namespace App\Services;

use App\Entity\CommentForTopics;
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
    public function handleTopicCommentsForm(FormInterface $topicCommentsForm): JsonResponse
    {
        if ($topicCommentsForm->isValid())
        {
            return $this->handleValidForm($topicCommentsForm);
        }
        else
        {
            return $this->handleInValidForm($topicCommentsForm);
        }
    }

    /**
     * @throws \Twig\Error\SyntaxError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\LoaderError
     */
    public function handleValidForm(FormInterface $form): JsonResponse
    {
        /**
         * @var CommentForTopics $comment
         */
        $comment = $form->getData();
//        ->setContent($data->getContent());

        $request = new Request();
        $request->setSession(new Session());
        $request->getSession()->getFlashBag()->add(
          'success',
          'Votre commentaire a été ajouté. actualiser la page pour voir les autres commentaires...'
        );

        return new JsonResponse([
            'response'=> CommentForTopics::COMMENT_ADDED_SUCCESSFULLY,
            'html'=> $this->environment->render("topic/brandTopicComment.html.twig", [
                'comment' => $comment,
                'slug'=> $comment->getSlug()
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
    public function handleEditCommentForm(FormInterface $form): JsonResponse
    {
        /**
         * @var CommentForTopics $comment
         */
        $comment = $form->getData();

        $request = new Request();
        $request->setSession(new Session());
        $request->getSession()->getFlashBag()->add(
            'notice',
            'Votre commentaire a été mis à jour. retourner vers la page précédente puis actualiser pour voir votre modifications...'
        );

        return new JsonResponse([
            'response'=> CommentForTopics::COMMENT_UPDATED_SUCCESSFULLY,
            'id'=> $comment->getId(),
            'html'=> $this->environment->render("topic/brandTopicComment.html.twig", [
                'comment' => $comment,
                'slug'=> $comment->getSlug()
            ]),
            'html_flash'=>$this->environment->render("topic/flash_edit.html.twig")
        ]);
    }
}
