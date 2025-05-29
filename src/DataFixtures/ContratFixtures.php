<?php

namespace App\DataFixtures;

use App\Entity\Contrat;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ContratFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $numeroBase = 1000;

        for ($i = 1; $i <= 5; $i++) { // Pour chaque utilisateur
            $utilisateur = $this->getReference('utilisateur_' . $i , Utilisateur::class);

            $nombreContrats = rand(1, 3);

            for ($j = 1; $j <= $nombreContrats; $j++) {
                $contrat = new Contrat();

                $contrat->setIdUtilisateur($utilisateur);

                $numeroContrat = 'CTR-' . ($numeroBase + $i * 10 + $j);
                $contrat->setNumeroContrat($numeroContrat);

                $contrat->setIntitule("Contrat $numeroContrat");

                $contrat->setDescription("Description du contrat numéro $numeroContrat, avec des détails succincts.");

                $now = new \DateTimeImmutable();

                // Dates prévues : début prévue dans le passé ou futur proche, fin prévue après début
                $dateDebutPrevue = (clone $now)->modify("-" . rand(30, 60) . " days");
                $dateFinPrevue = (clone $dateDebutPrevue)->modify("+ " . rand(30, 90) . " days");

                $contrat->setDateDebutPrevue($dateDebutPrevue);
                $contrat->setDateFinPrevue($dateFinPrevue);

                // Dates réelles : début entre début prévue et fin prévue, fin parfois vide
                $dateDebut = (clone $dateDebutPrevue)->modify("+" . rand(0, 5) . " days");
                $contrat->setDateDebut($dateDebut);

                // 50% des cas dateFin vide, sinon entre dateDebut et dateFinPrevue
                if (rand(0, 1) === 1) {
                    $dateFin = (clone $dateDebut)->modify("+ " . rand(10, 50) . " days");
                    if ($dateFin > $dateFinPrevue) {
                        $dateFin = $dateFinPrevue;
                    }
                    $contrat->setDateFin($dateFin);
                } else {
                    $contrat->setDateFin(null);
                }

                $cheminFichier = sprintf(
                    "var/storage/utilisateurs/%d/contrat/contrat_%s.pdf",
                    $utilisateur->getId() ?? $i, // si id pas encore généré, on met $i (sera à ajuster si besoin)
                    strtolower(str_replace([' ', '-'], ['_', '_'], $numeroContrat))
                );
                $contrat->setCheminFichier($cheminFichier);

                $contrat->setDateHeureInsertion(new \DateTimeImmutable());
                $contrat->setDateHeureMAJ(null);

                $manager->persist($contrat);

                $this->addReference("contrat_{$i}_{$j}", $contrat);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UtilisateurFixtures::class,
        ];
    }
}
