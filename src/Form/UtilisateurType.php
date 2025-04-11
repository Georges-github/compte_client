<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('courriel' , TextType::class )
            ->add('roles' , ChoiceType::class , [
                'choices' => Utilisateur::getLesRoles() ,
                'placeholder' => 'Choisir un role' ,
                'multiple' => true ,
                'expanded' => true
            ])
            // ->add('password' , TextType::class )
            ->add('prenom' , TextType::class )
            ->add('nom' , TextType::class )
            ->add('genre', ChoiceType::class , [
                'choices' => Utilisateur::getGenres() ,
                'placeholder' => 'Choisir un genre'
            ])
            ->add('telephone' , TextType::class )
            ->add('rueEtNumero' , TextType::class )
            ->add('codePostal' , TextType::class )
            ->add('ville' , TextType::class )
            ->add('societe' , TextType::class )
            // ->add('dateHeureInsertion', null, [
            //     'widget' => 'single_text'
            // ])
            // ->add('dateHeureMAJ', null, [
            //     'widget' => 'single_text'
            // ]);
            ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class
        ]);
    }
}
