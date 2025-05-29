<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
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

            // 🔐 Hash du mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, 'AAazerty11!');
            $utilisateur->setPassword($hashedPassword);

            $manager->persist($utilisateur);
            $this->addReference('utilisateur_' . $i, $utilisateur);
        }

        $manager->flush();
    }
}
