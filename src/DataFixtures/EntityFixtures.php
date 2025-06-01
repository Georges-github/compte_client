<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Contrat;
use App\Entity\EtatContrat;
use App\Entity\Publication;
use App\Entity\Commentaire;
use App\Entity\Photo;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EntityFixtures extends Fixture
{
    private $lesReferences = [];

    private UserPasswordHasherInterface $passwordHasher;

    private const MAX_COMMENTAIRE_NIVEAU = 2;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // 1. Utilisateurs
        $this->loadUtilisateurs($manager);

        // 2. Contrats
        $this->loadContrats($manager);

        // 3. EtatsContrat
        $this->loadEtatsContrat($manager);

        // 4. Publications
        $this->loadPublications($manager);

        // 5. Commentaires (avec sous-commentaires)
        $this->loadCommentaires($manager);

        // 6. Photos (pour publications et commentaires)
        $this->loadPhotos($manager);

        $manager->flush();
    }

    private function loadUtilisateurs(ObjectManager $manager): void
    {
        $prenomsHommes = ['Pierre', 'Julien', 'Antoine', 'Marc', 'Luc'];
        $prenomsFemmes = ['Marie', 'Claire', 'Sophie', 'Anne', 'Julie'];
        $noms = ['Dupont', 'Lemoine', 'Bernard', 'Moreau', 'Fabre'];

        for ($i = 1; $i <= 5; $i++) {
            $utilisateur = new Utilisateur();

            if ($i % 2 === 0) {
                $prenom = $prenomsFemmes[$i - 1];
                $genre = Utilisateur::GENRE_FEMME;
            } else {
                $prenom = $prenomsHommes[$i - 1];
                $genre = Utilisateur::GENRE_HOMME;
            }
            $nom = $noms[$i - 1];

            $utilisateur->setPrenom($prenom);
            $utilisateur->setNom($nom);
            $utilisateur->setGenre($genre);
            $utilisateur->setCourriel("$i" . "_georges.dreiding@sfr.fr");
            $utilisateur->setTelephoneMobile('06 68 65 26 72');
            $utilisateur->setRueEtNumero("10$i Rue de la Paix");
            $utilisateur->setCodePostal("7500$i");
            $utilisateur->setVille("Paris");
            $utilisateur->setDateHeureInsertion(new \DateTimeImmutable());
            $utilisateur->setRoles([
                Utilisateur::ROLE_UTILISATEUR,
                Utilisateur::ROLE_CLIENT,
            ]);
            $utilisateur->setMediasDeContact([
                Utilisateur::MDC_SMS,
                Utilisateur::MDC_WHATSAPP,
                Utilisateur::MDC_COURRIEL,
            ]);
            $utilisateur->setIsVerified(true);

            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, 'AAazerty11!');
            $utilisateur->setPassword($hashedPassword);

            $manager->persist($utilisateur);
            $this->addReference('utilisateur_' . $i, $utilisateur);
            $this->lesReferences['utilisateur_' . $i] = $utilisateur;
        }
        $manager->flush();
    }

    // private function loadContrats(ObjectManager $manager): void
    // {
    //     $numeroBase = 1000;

    //     for ($i = 1; $i <= 5; $i++) {
    //         /** @var Utilisateur $utilisateur */
    //         $utilisateur = $this->getReference('utilisateur_' . $i,Utilisateur::class);

    //         $nombreContrats = rand(1, 3);

    //         for ($j = 1; $j <= $nombreContrats; $j++) {
    //             $contrat = new Contrat();

    //             $contrat->setIdUtilisateur($utilisateur);

    //             $numeroContrat = 'CTR-' . ($numeroBase + $i * 10 + $j);
    //             $contrat->setNumeroContrat($numeroContrat);

    //             $contrat->setIntitule("Contrat $numeroContrat");

    //             $contrat->setDescription("Description du contrat numéro $numeroContrat, avec des détails succincts.");

    //             $now = new \DateTimeImmutable();

    //             $dateDebutPrevue = (clone $now)->modify("-" . rand(30, 60) . " days");
    //             $dateFinPrevue = (clone $dateDebutPrevue)->modify("+ " . rand(30, 90) . " days");

    //             $contrat->setDateDebutPrevue($dateDebutPrevue);
    //             $contrat->setDateFinPrevue($dateFinPrevue);

    //             $dateDebut = (clone $dateDebutPrevue)->modify("+" . rand(0, 5) . " days");
    //             $contrat->setDateDebut($dateDebut);

    //             if (rand(0, 1) === 1) {
    //                 $dateFin = (clone $dateDebut)->modify("+ " . rand(10, 50) . " days");
    //                 if ($dateFin > $dateFinPrevue) {
    //                     $dateFin = $dateFinPrevue;
    //                 }
    //                 $contrat->setDateFin($dateFin);
    //             } else {
    //                 $contrat->setDateFin(null);
    //             }

    //             $cheminFichier = sprintf(
    //                 "utilisateurs/%d/contrat/contrat_%s.pdf",
    //                 $utilisateur->getId() ?? $i,
    //                 strtolower(str_replace([' ', '-'], ['_', '_'], $numeroContrat))
    //             );
    //             $contrat->setCheminFichier($cheminFichier);

    //             $contrat->setDateHeureInsertion(new \DateTimeImmutable());
    //             $contrat->setDateHeureMAJ(null);

    //             $manager->persist($contrat);

    //             $this->addReference("contrat_{$i}_{$j}", $contrat);
    //             $this->lesReferences["contrat_{$i}_{$j}"] = $contrat;
    //         }
    //     }
    //     $manager->flush();
    // }

private function loadContrats(ObjectManager $manager): void
{
    $numeroBase = 1000;

    $sourceDir = 'var/storage/vivierContrats/';
    $sourcePDFs = glob($sourceDir . '*.pdf');

    if (empty($sourcePDFs)) {
        throw new \RuntimeException("Aucun fichier PDF trouvé dans $sourceDir");
    }

    for ($i = 1; $i <= 5; $i++) {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getReference('utilisateur_' . $i, Utilisateur::class);

        $nombreContrats = rand(1, 3);

        for ($j = 1; $j <= $nombreContrats; $j++) {
            $contrat = new Contrat();
            $contrat->setIdUtilisateur($utilisateur);

            $numeroContrat = 'CTR-' . ($numeroBase + $i * 10 + $j);
            $contrat->setNumeroContrat($numeroContrat);
            $contrat->setIntitule("Contrat $numeroContrat");
            $contrat->setDescription("Description du contrat numéro $numeroContrat, avec des détails succincts.");

            $now = new \DateTimeImmutable();
            $dateDebutPrevue = (clone $now)->modify("-" . rand(30, 60) . " days");
            $dateFinPrevue = (clone $dateDebutPrevue)->modify("+ " . rand(30, 90) . " days");

            $contrat->setDateDebutPrevue($dateDebutPrevue);
            $contrat->setDateFinPrevue($dateFinPrevue);

            $dateDebut = (clone $dateDebutPrevue)->modify("+" . rand(0, 5) . " days");
            $contrat->setDateDebut($dateDebut);

            if (rand(0, 1) === 1) {
                $dateFin = (clone $dateDebut)->modify("+ " . rand(10, 50) . " days");
                if ($dateFin > $dateFinPrevue) {
                    $dateFin = $dateFinPrevue;
                }
                $contrat->setDateFin($dateFin);
            } else {
                $contrat->setDateFin(null);
            }

            // Construction du chemin
            $cheminFichier = sprintf(
                "utilisateurs/%d/contrat/contrat_%s.pdf",
                $utilisateur->getId() ?? $i,
                strtolower(str_replace([' ', '-'], ['_', '_'], $numeroContrat))
            );
            $contrat->setCheminFichier($cheminFichier);

            $contrat->setDateHeureInsertion(new \DateTimeImmutable());
            $contrat->setDateHeureMAJ(null);

            // Copie du fichier PDF
            $destinationDir = sprintf('var/storage/utilisateurs/%d/contrat/', $utilisateur->getId());
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0777, true);
            }

            $sourceFile = $sourcePDFs[array_rand($sourcePDFs)];
            $destinationFile = $destinationDir . basename($cheminFichier);
            copy($sourceFile, $destinationFile);

            $manager->persist($contrat);

            $this->addReference("contrat_{$i}_{$j}", $contrat);
            $this->lesReferences["contrat_{$i}_{$j}"] = $contrat;
        }
    }

    $manager->flush();
}
    // private function loadEtatsContrat(ObjectManager $manager): void
    // {
    //     // $etatsPossibles = [
    //     //     EtatContrat::ETAT_EN_DISCUSSION,
    //     //     EtatContrat::ETAT_A_VENIR,
    //     //     EtatContrat::ETAT_EN_COURS,
    //     //     EtatContrat::ETAT_EN_PAUSE,
    //     //     EtatContrat::ETAT_ANNULE,
    //     //     EtatContrat::ETAT_TERMINE,
    //     // ];

    //     $etatsPossibles = [
    //         'ETAT_EN_DISCUSSION',
    //         'ETAT_A_VENIR',
    //         'ETAT_EN_COURS',
    //         'ETAT_EN_PAUSE',
    //         'ETAT_ANNULE',
    //         'ETAT_TERMINE'
    //     ];

    //     for ($i = 1; $i <= 5; $i++) {
    //         $nombreContrats = rand(1, 3);

    //         for ($j = 1; $j <= $nombreContrats; $j++) {
    //             try {
    //                 /** @var Contrat $contrat */
    //                 $contrat = $this->getReference("contrat_{$i}_{$j}",Contrat::class);
    //             } catch (\Exception $e) {
    //                 continue;
    //             }

    //             $utilisateur = $contrat->getIdUtilisateur();

    //             $nombreEtats = rand(2, 5);

    //             $dateBase = $contrat->getDateHeureInsertion() ?: new \DateTimeImmutable('-90 days');

    //                 $etatContrat = new EtatContrat();

    //                 $etatContrat->setEtat($etatsPossibles[0]);

    //                 $dateInsertion = $dateBase->modify("+ " . (1 * 7) . " days");
    //                 $etatContrat->setDateHeureInsertion($dateInsertion);

    //                 $etatContrat->setDateHeureMAJ(null);

    //                 $etatContrat->setIdUtilisateur($utilisateur);
    //                 $etatContrat->setIdContrat($contrat);

    //                 $manager->persist($etatContrat);

    //                 $etatContrat = new EtatContrat();

    //                 $etatContrat->setEtat($etatsPossibles[1]);

    //                 $dateInsertion = $dateBase->modify("+ " . (2 * 7) . " days");
    //                 $etatContrat->setDateHeureInsertion($dateInsertion);

    //                 $etatContrat->setDateHeureMAJ(null);

    //                 $etatContrat->setIdUtilisateur($utilisateur);
    //                 $etatContrat->setIdContrat($contrat);

    //                 $manager->persist($etatContrat);

    //             // for ($k = 0; $k < $nombreEtats; $k++) {
    //             //     $etatContrat = new EtatContrat();

    //             //     $etatIndex = min($k, count($etatsPossibles) - 1);
    //             //     $etatContrat->setEtat($etatsPossibles[$etatIndex]);

    //             //     $dateInsertion = $dateBase->modify("+ " . ($k * 7) . " days");
    //             //     $etatContrat->setDateHeureInsertion($dateInsertion);

    //             //     $etatContrat->setDateHeureMAJ(null);

    //             //     $etatContrat->setIdUtilisateur($utilisateur);
    //             //     $etatContrat->setIdContrat($contrat);

    //             //     $manager->persist($etatContrat);
    //             // }
    //         }
    //     }
    //     $manager->flush();
    // }

    private function loadEtatsContrat(ObjectManager $manager): void
{
    $etatsPossibles = [
        EtatContrat::ETAT_EN_DISCUSSION,
        EtatContrat::ETAT_A_VENIR,
        EtatContrat::ETAT_EN_COURS,
        EtatContrat::ETAT_EN_PAUSE,
        EtatContrat::ETAT_ANNULE,
        EtatContrat::ETAT_TERMINE,
    ];

    foreach ($this->lesReferences as $refName => $refObj) {
        if (str_starts_with($refName, 'contrat_')) {
            /** @var Contrat $contrat */
            $contrat = $refObj;
            $utilisateur = $contrat->getIdUtilisateur();

            $nombreEtats = rand(2, 5); // Tu veux toujours au moins 2 états
            $dateBase = $contrat->getDateHeureInsertion() ?? new \DateTimeImmutable('-90 days');

            for ($k = 0; $k < $nombreEtats; $k++) {
                $etatContrat = new EtatContrat();

                $etatIndex = min($k, count($etatsPossibles) - 1);
                $etatContrat->setEtat($etatsPossibles[$etatIndex]);

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


    private function loadPublications(ObjectManager $manager): void
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

        for ($i = 1; $i <= 5; $i++) {
            $nombreContrats = rand(1, 3);

            for ($j = 1; $j <= $nombreContrats; $j++) {
                try {
                    /** @var Contrat $contrat */
                    $contrat = $this->getReference("contrat_{$i}_{$j}",Contrat::class);
                } catch (\Exception $e) {
                    continue;
                }

                $utilisateur = $contrat->getIdUtilisateur();

                $nombrePublications = rand(1, 5);

                for ($k = 1; $k <= $nombrePublications; $k++) {
                    $publication = new Publication();

                    $titre = $titres[array_rand($titres)];
                    $contenu = $contenus[array_rand($contenus)];

                    $publication->setTitre($titre);
                    $publication->setContenu($contenu);

                    $dateInsertion = $contrat->getDateHeureInsertion() ?: new \DateTimeImmutable('-60 days');
                    $dateInsertion = $dateInsertion->modify('+' . (7 * $k) . ' days');
                    $publication->setDateHeureInsertion($dateInsertion);

                    $publication->setDateHeureMAJ(null);

                    $publication->setIdUtilisateur($utilisateur);
                    $publication->setIdContrat($contrat);

                    $manager->persist($publication);

                    $this->addReference("publication_{$i}_{$j}_{$k}", $publication);
                    $this->lesReferences["publication_{$i}_{$j}_{$k}"] = $publication;
                }
            }
        }
        $manager->flush();
    }

    private function loadCommentaires(ObjectManager $manager): void
    {
        $textes = [
            "Merci pour cette info.",
            "Très intéressant !",
            "Je suis d'accord avec vous.",
            "Pourriez-vous préciser ?",
            "Bonne idée, à approfondir.",
            "Cela mérite réflexion.",
            "Excellente proposition.",
            "Je reviendrai plus tard.",
            "Je ne suis pas convaincu.",
            "À revoir dans la prochaine réunion."
        ];

        $generateSousCommentaires = function (
            ObjectManager $manager,
            Commentaire $parent,
            int $niveau
        ) use (&$generateSousCommentaires, $textes) {
            if ($niveau >= self::MAX_COMMENTAIRE_NIVEAU) {
                return;
            }
            $nbSousCommentaires = rand(0, 2);

            for ($i = 0; $i < $nbSousCommentaires; $i++) {
                $commentaire = new Commentaire();
                $commentaire->setTexte($textes[array_rand($textes)]);
                $commentaire->setDateHeureInsertion(new \DateTimeImmutable());
                $commentaire->setDateHeureMAJ(null);
                $commentaire->setIdPublication($parent->getIdPublication());
                $commentaire->setIdCommentaireParent($parent);
                $commentaire->setIdUtilisateur($parent->getIdUtilisateur());

                $manager->persist($commentaire);

                $spl_object_hash = spl_object_hash($commentaire);
                $this->addReference('commentaire_' . $spl_object_hash, $commentaire);
                $this->lesReferences['commentaire_' . $spl_object_hash] = $commentaire;
                echo('commentaire_' . $spl_object_hash . "\n");

                $generateSousCommentaires($manager, $commentaire, $niveau + 1);
            }
        };

        // foreach ($this->getReferences() as $refName => $refObj) {
        foreach ($this->lesReferences as $refName => $refObj) {
            if (strpos($refName, 'publication_') === 0) {
                /** @var Publication $publication */
                $publication = $refObj;
                $utilisateur = $publication->getIdUtilisateur();

                $nbCommentaires = rand(3, 7);

                for ($i = 0; $i < $nbCommentaires; $i++) {
                    $commentaire = new Commentaire();
                    $commentaire->setTexte($textes[array_rand($textes)]);
                    $commentaire->setDateHeureInsertion(new \DateTimeImmutable());
                    $commentaire->setDateHeureMAJ(null);
                    $commentaire->setIdPublication($publication);
                    $commentaire->setIdCommentaireParent(null);
                    $commentaire->setIdUtilisateur($utilisateur);

                    $manager->persist($commentaire);

                    $spl_object_hash = spl_object_hash($commentaire);
                    $this->addReference('commentaire_' . $spl_object_hash, $commentaire);
                    $this->lesReferences['commentaire_' . $spl_object_hash] = $commentaire;
                    echo('commentaire_' . $spl_object_hash . "\n");

                    $generateSousCommentaires($manager, $commentaire, 1);
                }
            }
        }
        $manager->flush();
    }

    // private function loadPhotos(ObjectManager $manager): void
    // {
    //     echo("loadPhotos\n");
    //     $legendes = [
    //         'Photo accueil',
    //         'Image produit',
    //         'Portrait client',
    //         'Vue générale',
    //         'Événement',
    //         'Document scanné',
    //         'Photo d’équipe',
    //         'Plan de salle',
    //         'Photo souvenir',
    //         'Image illustrative'
    //     ];

    //     // Photos pour publications
    //     // foreach ($this->getReferences() as $refName => $refObj) {
    //     foreach ($this->lesReferences as $refName => $refObj) {
    //         if (strpos($refName, 'publication_') === 0) {
    //             /** @var Publication $publication */
    //             $publication = $refObj;
    //             $utilisateur = $publication->getIdUtilisateur();

    //             if (mt_rand(1, 100) <= 70) {
    //                 $photo = new Photo();
    //                 $photo->setLegende($legendes[array_rand($legendes)]);
    //                 $photo->setCheminFichierImage(
    //                     "utilisateurs/{$utilisateur->getId()}/image/photo_pub_{$publication->getId()}.jpg"
    //                 );
    //                 $photo->setDateHeureInsertion(new \DateTimeImmutable());
    //                 $photo->setDateHeureMAJ(null);
    //                 $photo->setIdPublication($publication);
    //                 $photo->setIdCommentaire(null);

    //                 $manager->persist($photo);
    //             }
    //         }
    //     }

    //     // Photos pour commentaires
    //     // foreach ($this->getReferences() as $refName => $refObj) {
    //     foreach ($this->lesReferences as $refName => $refObj) {
    //         if (strpos($refName, 'commentaire_') === 0) {
    //             /** @var Commentaire $commentaire */
    //             $commentaire = $refObj;
    //             $utilisateur = $commentaire->getIdUtilisateur();

    //             if (mt_rand(1, 100) <= 30) {
    //                 $photo = new Photo();
    //                 $photo->setLegende($legendes[array_rand($legendes)]);
    //                 $photo->setCheminFichierImage(
    //                     "utilisateurs/{$utilisateur->getId()}/image/photo_com_{$commentaire->getId()}.jpg"
    //                 );
    //                 $photo->setDateHeureInsertion(new \DateTimeImmutable());
    //                 $photo->setDateHeureMAJ(null);
    //                 $photo->setIdPublication(null);
    //                 $photo->setIdCommentaire($commentaire);

    //                 $manager->persist($photo);
    //             }
    //         }
    //     }
    // }

private function loadPhotos(ObjectManager $manager): void
{
    echo("loadPhotos\n");
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

    $sourceDir = 'var/storage/vivierPhotos/';
    $sourcePhotos = glob($sourceDir . '*.jpg');

    if (empty($sourcePhotos)) {
        throw new \RuntimeException("Aucune image source trouvée dans $sourceDir");
    }

    // Photos pour publications
    foreach ($this->lesReferences as $refName => $refObj) {
        if (strpos($refName, 'publication_') === 0) {
            /** @var Publication $publication */
            $publication = $refObj;
            $utilisateur = $publication->getIdUtilisateur();

            if (mt_rand(1, 100) <= 70) {
                $photo = new Photo();
                $photo->setLegende($legendes[array_rand($legendes)]);
                $targetPath = "utilisateurs/{$utilisateur->getId()}/image/photo_pub_{$publication->getId()}.jpg";
                $photo->setCheminFichierImage($targetPath);
                $photo->setDateHeureInsertion(new \DateTimeImmutable());
                $photo->setDateHeureMAJ(null);
                $photo->setIdPublication($publication);
                $photo->setIdCommentaire(null);

                // Choisir une image au hasard
                $sourceFile = $sourcePhotos[array_rand($sourcePhotos)];
                $destinationDir = "var/storage/utilisateurs/{$utilisateur->getId()}/image/";
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }
                copy($sourceFile, $destinationDir . "photo_pub_{$publication->getId()}.jpg");

                $manager->persist($photo);
            }
        }
    }

    // Photos pour commentaires
    foreach ($this->lesReferences as $refName => $refObj) {
        if (strpos($refName, 'commentaire_') === 0) {
            /** @var Commentaire $commentaire */
            $commentaire = $refObj;
            $utilisateur = $commentaire->getIdUtilisateur();

            if (mt_rand(1, 100) <= 30) {
                $photo = new Photo();
                $photo->setLegende($legendes[array_rand($legendes)]);
                $targetPath = "utilisateurs/{$utilisateur->getId()}/image/photo_com_{$commentaire->getId()}.jpg";
                $photo->setCheminFichierImage($targetPath);
                $photo->setDateHeureInsertion(new \DateTimeImmutable());
                $photo->setDateHeureMAJ(null);
                $photo->setIdPublication(null);
                $photo->setIdCommentaire($commentaire);

                // Choisir une image au hasard
                $sourceFile = $sourcePhotos[array_rand($sourcePhotos)];
                $destinationDir = "var/storage/utilisateurs/{$utilisateur->getId()}/image/";
                if (!is_dir($destinationDir)) {
                    mkdir($destinationDir, 0777, true);
                }
                copy($sourceFile, $destinationDir . "photo_com_{$commentaire->getId()}.jpg");

                $manager->persist($photo);
            }
        }
    }
}
    /**
     * Récupérer toutes les références existantes
     * (reproduit partiellement le comportement de getReferences() privé)
     */
    private function getReferences(): array
    {
        $reflection = new \ReflectionClass(Fixture::class);
        $prop = $reflection->getProperty('references');
        $prop->setAccessible(true);
        return $prop->getValue($this);
    }
}
