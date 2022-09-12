<?php

namespace App\Form;

use App\Entity\CommentForTopics;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

class CommentForTopicsFormType extends AbstractType
{
    public function __construct(
        private RouterInterface $router
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('content', CKEditorType::class);
        /**
         * @var CommentForTopics $comment
         */
        $comment = $builder->getData();

        if ($comment === null || $comment->getId() === null)
        {
            $builder->setAction($this->router->generate('app_topic_add_comment', [
                'slug'=> $options['topicSlug']
            ]));

        } else {
            $builder->setAction($this->router->generate('app_topic_edit_comment', [
                'slug' => $options['topicSlug'],
                'id' => $comment->getId()
            ]));
        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommentForTopics::class,
            'topicSlug' => ''
        ]);
    }
}
