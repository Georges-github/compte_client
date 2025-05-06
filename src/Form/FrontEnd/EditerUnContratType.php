<?php

namespace App\Form\FrontEnd;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditerUnContratType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'intitule' )
        ->add( 'description' )
        ->add( 'numéro' )
        ->add( 'dateDebutPrevue' )
        ->add( 'dateFinPrevue' )
        ->add( 'dateDebut' )
        ->add( 'dateFin' )
        ->add('submit', SubmitType::class, [ 'label' => 'Ajouter' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm'] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Contrat::class ] );
    }

}