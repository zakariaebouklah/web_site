<?php

namespace App\Form;

use App\Entity\Subscription;
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
            ->add('ateliersDeFormation', ChoiceType::class, [
                'choices'=>[
                    'Le doctorat n’est pas un long fleuve tranquille (DEKHISSI Ilyass - EST - UMP Oujda)'=>'Le doctorat n’est pas un long fleuve tranquille (DEKHISSI Ilyass - EST - UMP Oujda)',
                    'Développer son esprit scientifique par les 2C : Connaissance & Conscience  (MAJIDI Fouzia - FSJES - UMP Oujda)'=>'Développer son esprit scientifique par les 2C : Connaissance & Conscience  (MAJIDI Fouzia - FSJES - UMP Oujda)',
                    'La motivation dans la recherche scientifique (BERRICHI Abdelouahed - FSJES - UMP Oujda)'=>'La motivation dans la recherche scientifique (BERRICHI Abdelouahed - FSJES - UMP Oujda)',
                    'Spécifier son objet de recherche (EL ATTAR Abdellilah - FSJES - UMP Oujda)'=>'Spécifier son objet de recherche (EL ATTAR Abdellilah - FSJES - UMP Oujda)',
                    'Conduire son projet de recherche selon la perspective quantitative et qualitative (Karim BENNIS - FSJES - USMBA Fès)'=>'Conduire son projet de recherche selon la perspective quantitative et qualitative (Karim BENNIS - FSJES - USMBA Fès)',
                    'Design de recherche : construire son modèle de recherche dans une perspective hypothético-déductive (HAFIANE Mohammed Amine - FSJES - UMP Oujda)'=>'Design de recherche : construire son modèle de recherche dans une perspective hypothético-déductive (HAFIANE Mohammed Amine - FSJES - UMP Oujda)',
                    'Réussir son étude empirique : De l’opérationnalisation à la modélisation par les équations structurelles et la vérification des hypothèses (EDDAOU Mohammed - FSJES- UMP Oujda)'=>'Réussir son étude empirique : De l’opérationnalisation à la modélisation par les équations structurelles et la vérification des hypothèses (EDDAOU Mohammed - FSJES- UMP Oujda)',
                    'L’art de rédiger son article scientifique (FIKRI Khalid - FSJES - UMP Oujda)'=>'L’art de rédiger son article scientifique (FIKRI Khalid - FSJES - UMP Oujda)',
                ],
                'multiple'=>true,
                'expanded'=>true
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
