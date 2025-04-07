<?php

namespace App\Factory;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends PersistentProxyObjectFactory<Utilisateur>
 */
final class UtilisateurFactory extends PersistentProxyObjectFactory {

    public static int $_occurrence = 0;

    private static $_u = [
        [ "prenom" => "Clara" , "nom" => "Israel", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "contact@ad-clo.fr" , "motDePasse" => "az" , "telephone" => "+33 1 02 03 04 05" , "numeroEtRue" => "151 rue des Rabats" , "codePostal" => "92160" , "ville" => "Antony" , "societe" => "CLO architecture" , "role" => "ADMINISTRATEUR" ] ,
        [ "prenom" => "Aurélien" , "nom" => "Avert", "genre" => Utilisateur::GENRE_HOMME , "courriel" => "aurelien.Avert@ad-clo.fr" , "motDePasse" => "qs" , "telephone" => "+33 1 06 07 08 09" , "numeroEtRue" => "1 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => "ADMINISTRATEUR" ] ,
        [ "prenom" => "Eléonor" , "nom" => "Majault", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "eleonor.majault@ad-clo.fr" , "motDePasse" => "wx" , "telephone" => "+33 1 10 11 12 13" , "numeroEtRue" => "2 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => "EMPLOYE" ] ,
        [ "prenom" => "Anaïs" , "nom" => "Molliex", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "anais.molliex@ad-clo.fr" , "motDePasse" => "er" , "telephone" => "+33 1 14 15 16 17" , "numeroEtRue" => "3 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => "EMPLOYE"]
    ];

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct( private UserPasswordHasherInterface $hasher )
    {
    }

    public static function class(): string
    {
        return Utilisateur::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $utilisateur = new Utilisateur();

        $u = self::$_u[ self::$_occurrence++ ];

        return [
            'codePostal' => $u[ "codePostal" ] ,
            'courriel' => $u[ "courriel" ] ,
            'dateHeureInsertion' => new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) ,
            'genre' => $u[ "genre" ] ,
            'nom' => $u[ "nom" ] ,
            'password' => $this->hasher->hashPassword( $utilisateur , $u[ "motDePasse" ] ) ,
            'prenom' => $u[ "prenom" ] ,
            'roles' => [ $u[ "role" ] ],
            'rueEtNumero' => $u[ "numeroEtRue" ] ,
            'societe' => $u[ "societe" ],
            'telephone' => $u[ "telephone" ],
            'ville' => $u[ "ville" ]
        ];
        // return [
        //     'codePostal' => self::faker()->text(20),
        //     'courriel' => self::faker()->text(180),
        //     'dateHeureInsertion' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
        //     'genre' => self::faker()->randomElement(EnumGenre::cases()),
        //     'nom' => self::faker()->text(50),
        //     'password' => self::faker()->text(),
        //     'prenom' => self::faker()->text(50),
        //     'roles' => [],
        //     'rueEtNumero' => self::faker()->text(255),
        //     'ville' => self::faker()->text(100),
        // ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Utilisateur $utilisateur): void {})
        ;
    }
}
