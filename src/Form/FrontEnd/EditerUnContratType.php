<?php

namespace App\Form\FrontEnd;

use App\Entity\Contrat;
use App\Entity\EtatContrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints\File;

class EditerUnContratType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $etatActuel = $options['etatActuel'];

        $labelSubmit = $options['edition'] ? 'Valider' : 'Ajouter';

        $b = $builder
        ->add( 'intitule' , null , [ 'label' => 'Intitulé' ] )
        ->add( 'description' , null , [ 'label' => '' ] )
        ->add( 'numeroContrat' , null , [ 'label' => 'numéro' ] )
        ->add('etatChoisi', ChoiceType::class, [
            'label' => 'État actuel du contrat',
            'choices' => EtatContrat::getLesEtats(),
            'mapped' => false,
            'placeholder' => 'Sélectionnez un état',
            'choice_attr' => function ($choice, $key, $value) use ($etatActuel) { return $value === $etatActuel ? ['selected' => 'selected'] : []; }
        ])
        ->add( 'dateDebutPrevue' , null , [ 'label' => 'Date de début prévue' ]  )
        ->add( 'dateFinPrevue' , null , [ 'label' => 'Date de fin prévue' ]  )
        ->add( 'dateDebut' , null , [ 'label' => 'Date de début' ]  )
        ->add( 'dateFin' , null , [ 'label' => 'Date de fin' ]   )
        ->add( 'cheminFichier' , FileType::class ,
        [ 'label' => 'Fichier contrat'  ,
        'mapped' => false ,
        'required' => $options['edition'] ? false : true ,
        'constraints' => [
            new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Fichier PDF valide uniquement.',
                    ])
        ] ] )
        ->add('submit', SubmitType::class, [ 'label' => $labelSubmit ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm shadow'] ])
        ;

        // if ( $options['edition'] ) {
        //     $b->add('nomContratActuel', TextType::class, [
        //             'label' => 'Contrat actuel',
        //             'mapped' => false,
        //             'data' => $options['nomContratActuel'],
        //             'disabled' => true,
        //             'required' => false,
        //     ]);
        // }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Contrat::class , 'etatActuel' => null , 'nomContratActuel' => null , 'edition' => null ] );
    }

}