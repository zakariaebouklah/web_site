<?php

namespace App\Form;

use App\Entity\Atelier;
use App\Entity\Subscription;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName')
            ->add('UMPMail')
            ->add('phone')
            ->add('homeLaboratory')
            ->add('homeInstitution', ChoiceType::class, [
                'multiple'=>false,
                'expanded'=>true,
                'choices'=>[
                    'Faculté des Sciences Juridiques, Economiques et Sociales d\'Oujda'=>'Faculté des Sciences Juridiques, Economiques et Sociales d\'Oujda',
                    'Ecole Nationale de Commerce et de Gestion d\'Oujda'=>'Ecole Nationale de Commerce et de Gestion d\'Oujda',
                    'Ecole Supérieure de Technologie d\'Oujda'=>'Ecole Supérieure de Technologie d\'Oujda',
                    'Faculté Pluridisciplinaire de Nador'=>'Faculté Pluridisciplinaire de Nador',
                    'Autre...'=>'Autre...'
                ]
            ])
            ->add('searchTheme')
            ->add('inscriptionYear', ChoiceType::class, [
                'multiple'=>false,
                'expanded'=>true,
                'choices'=>[
                    'Première année'=>'Première année',
                    'Deuxième année'=>'Deuxième année',
                    'Troisième année'=>'Troisième année',
                    'Quatrième année'=>'Quatrième année',
                    'Cinquième année'=>'Cinquième année',
                    'Sixième année'=>'Sixième année',
                ]
            ])
            ->add('ateliers', EntityType::class , [
                'class'=>Atelier::class,
                'multiple'=>true,
                'expanded'=>true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
