<?php

namespace App\DataFixtures;

use App\Entity\Contrat;
use App\Entity\Publication;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PublicationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $titres = [
            "Mise à jour contrat",
            "Nouveautés service",
            "Avis important",
            "Modification planning",
            "Rappel échéance",
            "Annonce spéciale",
            "Note interne",
            "Information client",
            "Mise au point",
            "Bulletin trimestriel"
        ];

        $contenus = [
            "Voici les dernières informations concernant votre contrat.",
            "Veuillez prendre connaissance des nouveautés introduites.",
            "Important : vérifiez vos documents avant la date limite.",
            "Changement de planning, merci de vous adapter.",
            "Rappel : votre échéance arrive bientôt.",
            "Annonce d’une offre exclusive pour nos clients.",
            "Note importante sur la procédure interne.",
            "Information essentielle pour le bon suivi.",
            "Mise au point nécessaire avant la prochaine réunion.",
            "Bulletin trimestriel des activités récentes."
        ];

        // Pour chaque utilisateur (5 créés)
        for ($i = 1; $i <= 5; $i++) {
            $nombreContrats = rand(1, 3);

            for ($j = 1; $j <= $nombreContrats; $j++) {
                $contrat = $this->getReference("contrat_{$i}_{$j}" , Contrat::class);
                $utilisateur = $contrat->getIdUtilisateur();

                // Chaque contrat a entre 1 et 5 publications
                $nombrePublications = rand(1, 5);

                for ($k = 1; $k <= $nombrePublications; $k++) {
                    $publication = new Publication();

                    $titre = $titres[array_rand($titres)];
                    $contenu = $contenus[array_rand($contenus)];

                    $publication->setTitre($titre);
                    $publication->setContenu($contenu);

                    // Date insertion entre contrat dateHeureInsertion et maintenant
                    $dateInsertion = $contrat->getDateHeureInsertion() ?: new \DateTimeImmutable('-60 days');
                    $dateInsertion = $dateInsertion->modify('+'.(7 * $k).' days');
                    $publication->setDateHeureInsertion($dateInsertion);

                    $publication->setDateHeureMAJ(null);

                    $publication->setIdUtilisateur($utilisateur);
                    $publication->setIdContrat($contrat);

                    $manager->persist($publication);

                    // Pour pouvoir relier photos/commentaires après
                    $this->addReference("publication_{$i}_{$j}_{$k}", $publication);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ContratFixtures::class,
            UtilisateurFixtures::class,
        ];
    }
}
