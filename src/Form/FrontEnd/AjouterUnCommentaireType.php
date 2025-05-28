<?php

namespace App\Form\FrontEnd;

use App\Entity\Commentaire;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\File;

class AjouterUnCommentaireType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'texte' , TextareaType::class , [ 'label' => 'Commentaire' ] )
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
        ->add( 'submit', SubmitType::class, [ 'label' => 'Ajouter' ,
        'attr' => ['class' => 'btn btn-outline-light btn-sm shadow'] ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults( [ 'data_class' => Commentaire::class ] );
    }
}