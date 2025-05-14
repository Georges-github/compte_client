<?php

namespace App\Form;

use App\Entity\Contrat;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numeroContrat')
            ->add('intitule')
            ->add('description')
            ->add('dateDebutPrevue', null, [
                'widget' => 'single_text'
            ])
            ->add('dateFinPrevue', null, [
                'widget' => 'single_text'
            ])
            ->add('dateDebut', null, [
                'widget' => 'single_text'
            ])
            ->add('dateFin', null, [
                'widget' => 'single_text'
            ])
            ->add('cheminFichier')
            ->add('dateHeureInsertion', null, [
                'widget' => 'single_text'
            ])
            ->add('dateHeureMAJ', null, [
                'widget' => 'single_text'
            ])
            ->add('idUtilisateur', EntityType::class, [
                'class' => Utilisateur::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}
