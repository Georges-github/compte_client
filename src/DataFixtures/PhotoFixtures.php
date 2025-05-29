<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\Publication;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PhotoFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $legendes = [
            'Photo accueil',
            'Image produit',
            'Portrait client',
            'Vue générale',
            'Événement',
            'Document scanné',
            'Photo d’équipe',
            'Plan de salle',
            'Photo souvenir',
            'Image illustrative'
        ];

        // Générer photos pour Publications
        for ($i = 1; $i <= 5; $i++) { // Utilisateurs
            $nombreContrats = rand(1, 3);
            for ($j = 1; $j <= $nombreContrats; $j++) { // Contrats
                $nombrePublications = rand(1, 5);
                for ($k = 1; $k <= $nombrePublications; $k++) { // Publications
                    $publicationRefName = "publication_{$i}_{$j}_{$k}";
                    if (!$this->hasReference($publicationRefName , Publication::class)) {
                        continue;
                    }
                    $publication = $this->getReference($publicationRefName , Publication::class);
                    $utilisateur = $publication->getIdUtilisateur();

                    // Une seule photo par publication, 70% chance de l'avoir
                    if (mt_rand(1, 100) <= 70) {
                        $photo = new Photo();
                        $photo->setLegende($legendes[array_rand($legendes)]);
                        $photo->setCheminFichierImage(
                            "var/storage/utilisateurs/{$utilisateur->getId()}/image/photo_pub_{$publication->getId()}.jpg"
                        );
                        $photo->setDateHeureInsertion(new \DateTimeImmutable());
                        $photo->setDateHeureMAJ(null);
                        $photo->setIdPublication($publication);
                        $photo->setIdCommentaire(null);
                        $manager->persist($photo);
                    }
                }
            }
        }

        // Générer photos pour Commentaires (y compris sous-commentaires)
        // Parcours des références commentaires : on suppose max 7 commentaires par publication, plus sous-comments
        foreach ($this->getReferences() as $refName => $refObj) {
            if (strpos($refName, 'commentaire_') === 0) {
                /** @var \App\Entity\Commentaire $commentaire */
                $commentaire = $refObj;
                $utilisateur = $commentaire->getIdUtilisateur();

                // 30% chance d'avoir une photo
                if (mt_rand(1, 100) <= 30) {
                    $photo = new Photo();
                    $photo->setLegende($legendes[array_rand($legendes)]);
                    $photo->setCheminFichierImage(
                        "var/storage/utilisateurs/{$utilisateur->getId()}/image/photo_com_{$commentaire->getId()}.jpg"
                    );
                    $photo->setDateHeureInsertion(new \DateTimeImmutable());
                    $photo->setDateHeureMAJ(null);
                    $photo->setIdPublication(null);
                    $photo->setIdCommentaire($commentaire);
                    $manager->persist($photo);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PublicationFixtures::class,
            CommentaireFixtures::class,
            UtilisateurFixtures::class,
        ];
    }
}
