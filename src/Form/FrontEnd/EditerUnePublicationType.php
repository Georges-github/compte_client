<?php

namespace App\Form\FrontEnd;

use App\Entity\Publication;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\File;

class EditerUnePublicationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $labelSubmit = $options['edition'] ? 'Valider' : 'Ajouter';

        $builder
        ->add( 'titre' , TextType::class , [ 'label' => 'Titre' ] )
        ->add( 'contenu' , TextareaType::class , [ 'label' => 'Contenu' ] )
        ->add( 'image' , ButtonType::class , [ 'label' => 'Ajouter une image' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm mt-3'] ] )
        ->add( 'cheminFichierImage' , FileType::class ,
        [ 'label' => 'Image'  ,
        'mapped' => false ,
        'required' => false ,
        'constraints' => [
            new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Fichiers jpg, png, gif valides uniquement.',
                    ])
        ] ] )
        ->add('photos', CollectionType::class, [
            'entry_type' => PhotoType::class,
            'entry_options' => ['label' => false],
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false, // important si tu modifies la collection (add/remove)
        ])
        ->add( 'submit', SubmitType::class, [ 'label' => $labelSubmit ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm mt-3'] ] )
        ->add( 'annuler' , ButtonType::class , [ 'label' => 'Annuler' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm mt-3'] ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Publication::class , 'edition' => false ] );
    }
}