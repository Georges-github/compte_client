<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('prenom' , TextType::class )
        ->add('nom' , TextType::class )
        ->add('genre', ChoiceType::class , [
            'choices' => Utilisateur::getLesGenres() ,
            'expanded' => true
        ])
        ->add('courriel')
        ->add('roles' , ChoiceType::class , [
            'choices' => Utilisateur::getLesRoles() ,
            'multiple' => true ,
            'expanded' => true
        ])
        ->add('mediasDeContact' , ChoiceType::class , [
            'choices' => Utilisateur::getLesMediasDeContact() ,
            'multiple' => true ,
            'expanded' => true
        ])
        // ->add('mediasDeContact', ChoiceType::class , [
        //     'choices' => Utilisateur::getLesMediasDeContact() ,
        //     'expanded' => true
        // ])
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => 'You should agree to our terms.',
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ])
        ->add('telephoneFixe' , TextType::class )
        ->add('telephoneMobile' , TextType::class )
        ->add('rueEtNumero' , TextType::class )
        ->add('codePostal' , TextType::class )
        ->add('ville' , TextType::class )
        ->add('societe' , TextType::class )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
