<?php

namespace App\DataFixtures;

use App\Entity\Contrat;
use App\Entity\EtatContrat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EtatContratFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $etatsPossibles = [
            EtatContrat::ETAT_EN_DISCUSSION,
            EtatContrat::ETAT_A_VENIR,
            EtatContrat::ETAT_EN_COURS,
            EtatContrat::ETAT_EN_PAUSE,
            EtatContrat::ETAT_ANNULE,
            EtatContrat::ETAT_TERMINE,
        ];

        // Pour chaque contrat créé
        for ($i = 1; $i <= 5; $i++) {
            // Chaque utilisateur a entre 1 et 3 contrats
            $nombreContrats = rand(1, 3);

            for ($j = 1; $j <= $nombreContrats; $j++) {
                /** @var \App\Entity\Contrat $contrat */
                $contrat = $this->getReference("contrat_{$i}_{$j}" , Contrat::class);
                $utilisateur = $contrat->getIdUtilisateur();

                // Génération entre 2 et 5 états pour ce contrat
                $nombreEtats = rand(2, 5);

                // On crée un historique d'états successifs
                $dateBase = $contrat->getDateHeureInsertion() ?: new \DateTimeImmutable('-90 days');

                for ($k = 0; $k < $nombreEtats; $k++) {
                    $etatContrat = new EtatContrat();

                    $etatIndex = min($k, count($etatsPossibles) - 1);
                    $etatContrat->setEtat($etatsPossibles[$etatIndex]);

                    // Décaler la date d'insertion pour chaque état
                    $dateInsertion = $dateBase->modify("+ " . ($k * 7) . " days");
                    $etatContrat->setDateHeureInsertion($dateInsertion);

                    $etatContrat->setDateHeureMAJ(null);

                    $etatContrat->setIdUtilisateur($utilisateur);
                    $etatContrat->setIdContrat($contrat);

                    $manager->persist($etatContrat);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ContratFixtures::class,
        ];
    }
}
