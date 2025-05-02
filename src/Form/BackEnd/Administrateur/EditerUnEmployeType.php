<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
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
                'choices' => Utilisateur::getLesRoles() ,
                'multiple' => true ,
                'expanded' => true
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'mapped' => false, // si ce champ ne correspond pas directement à une propriété de l'entité
            ])
            ->add('prenom')
            ->add('nom')
            // ->add('genre')
            ->add('genre', ChoiceType::class , [
                'choices' => Utilisateur::getLesGenres() ,
                    'expanded' => true ,
                    'multiple' => false ,
                    'label' => 'Genre' ,
                    'placeholder' => 'Choisir un genre'
            ])
            ->add('telephoneFixe')
            ->add('telephoneMobile')
            ->add('rueEtNumero')
            ->add('codePostal')
            ->add('ville')
            ->add('societe')
            // ->add('dateHeureInsertion', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('dateHeureMAJ', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('isVerified')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
