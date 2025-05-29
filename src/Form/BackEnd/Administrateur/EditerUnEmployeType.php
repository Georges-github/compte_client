<?php

namespace App\Form\BackEnd\Administrateur;

use App\Entity\Utilisateur;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Component\OptionsResolver\OptionsResolver;

use function PHPSTORM_META\type;

class EditerUnEmployeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('courriel')
            // ->add('mediasDeContact')
            ->add('mediasDeContact' , ChoiceType::class , [
                'choices' => Utilisateur::getLesMediasDeContact() ,
                'multiple' => true ,
                'expanded' => true
            ])
            // ->add('roles')
            ->add('roles' , ChoiceType::class , [
                'choices' => Utilisateur::getLesRoles( 'EMPLOYE' ) ,
                'multiple' => true , 
                'attr' => ['size' => 5]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => !$options[ 'edition' ],
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => true
            ])
            ->add('prenom')
            ->add('nom')
            // ->add('genre')
            ->add('genre', ChoiceType::class , [
                'choices' => Utilisateur::getLesGenres() ,
                    'expanded' => true ,
                    'multiple' => false ,
                    'label' => 'Genre'
            ])
            ->add('telephoneFixe')
            ->add('telephoneMobile')
            ->add('rueEtNumero')
            ->add('codePostal')
            ->add('ville')
            ->add('societe')
            ->add('submit', SubmitType::class, [ 'label' => 'Mettre à jour' ,
            'attr' => ['class' => 'btn btn-outline-light btn-sm shadow'] ])
            // ->add('dateHeureInsertion', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('dateHeureMAJ', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'edition' => false
        ]);
    }
}
