<?php

namespace App\Form\FrontEnd;

use App\Entity\Publication;

use App\Form\FrontEnd\UniquePhotoType;

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

class UniqueEditerUnePublicationType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'titre' , TextType::class , [ 'label' => 'Titre' , 'attr' => ['class' => 'w-50'] ] )
        ->add( 'contenu' , TextareaType::class , [ 'label' => 'Contenu' , 'attr' => ['class' => 'w-50'] ] )
        ->add('legende', TextType::class, [
            'label' => 'Légende',
            'mapped' => false,
            'required' => false,
        ])
        ->add('photo', FileType::class, [
        'label' => 'Photo',
        'mapped' => false,
        'required' => false,
        'constraints' => [
            new File([
                'maxSize' => '2M',
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                'mimeTypesMessage' => 'Fichier invalide (JPEG, PNG ou WEBP seulement)',
                ]),
            ],
        ])
        ->add( 'submit', SubmitType::class, [ 'label' => 'Valider' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm'] ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Publication::class ] );
    }
}