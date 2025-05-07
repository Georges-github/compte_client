<?php

namespace App\Form\FrontEnd;

use App\Entity\Contrat;
use App\Entity\EtatContrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditerUnContratType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'intitule' , null , [ 'label' => 'Intitulé' ] )
        ->add( 'description' , null , [ 'label' => '' ] )
        ->add( 'numeroContrat' , null , [ 'label' => 'numéro' ] )
        ->add('etatChoisi', ChoiceType::class, [
            'label' => 'État initial du contrat',
            'choices' => EtatContrat::getLesEtats(),
            'mapped' => false,
            'placeholder' => 'Sélectionnez un état',
        ])
        ->add( 'dateDebutPrevue' , null , [ 'label' => 'Date de début prévue' ]  )
        ->add( 'dateFinPrevue' , null , [ 'label' => 'Date de fin prévue' ]  )
        ->add( 'dateDebut' , null , [ 'label' => 'Date de début' ]  )
        ->add( 'dateFin' , null , [ 'label' => 'Date de fin' ]   )
        ->add( 'cheminFichier' , FileType::class , [ 'label' => 'Fichier contrat' ]   )
        ->add('submit', SubmitType::class, [ 'label' => 'Ajouter' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm mt-3'] ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Contrat::class ] );
    }

}