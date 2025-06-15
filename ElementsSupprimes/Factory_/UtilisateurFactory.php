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
        [ "prenom" => "Clara" , "nom" => "Israel", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "contact@ad-clo.fr" , "motDePasse" => "CIazerty11!" , "telephoneFixe" => "+33 1 02 03 04 05" , "telephoneMobile" => "+33 6 02 03 04 05" , "mediaDeContact" => "SMS" , "numeroEtRue" => "151 rue des Rabats" , "codePostal" => "92160" , "ville" => "Antony" , "societe" => "CLO architecture" , "role" => Utilisateur::ROLE_EMPLOYE_ADMINISTRATEUR ] ,
        [ "prenom" => "Aurélien" , "nom" => "Avert", "genre" => Utilisateur::GENRE_HOMME , "courriel" => "aurelien.Avert@ad-clo.fr" , "motDePasse" => "AAazerty11!" , "telephoneFixe" => "+33 1 06 07 08 09" , "telephoneMobile" => "+33 6 06 07 08 09" , "mediaDeContact" => "SMS" , "numeroEtRue" => "1 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => Utilisateur::ROLE_EMPLOYE_ADMINISTRATEUR ] ,
        [ "prenom" => "Eléonor" , "nom" => "Majault", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "eleonor.majault@ad-clo.fr" , "motDePasse" => "EMazerty11!" , "telephoneFixe" => "+33 1 10 11 12 13" , "telephoneMobile" => "+33 6 10 11 12 13" , "mediaDeContact" => "SMS" , "numeroEtRue" => "2 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => Utilisateur::ROLE_EMPLOYE ] ,
        [ "prenom" => "Anaïs" , "nom" => "Molliex", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "anais.molliex@ad-clo.fr" , "motDePasse" => "AMazerty11!" , "telephoneFixe" => "+33 1 14 15 16 17" , "telephoneMobile" => "+33 6 14 15 16 17" , "mediaDeContact" => "SMS" , "numeroEtRue" => "3 rue de l'industrie" , "codePostal" => "74000" , "ville" => "Annecy" , "societe" => "CLO architecture" , "role" => Utilisateur::ROLE_EMPLOYE ] ,

        [ "prenom" => "Marc" , "nom" => "Dupont", "genre" => Utilisateur::GENRE_HOMME , "courriel" => "marc.dupont@ad-clo.fr" , "motDePasse" => "MDazerty11!" , "telephoneFixe" => "+33 1 19 20 21 22" , "telephoneMobile" => "+33 6 19 20 21 22" , "mediaDeContact" => "SMS" , "numeroEtRue" => "45 rue des Lilas" , "codePostal" => "75015" , "ville" => "Paris" , "societe" => "Indépendant" , "role" => Utilisateur::ROLE_CLIENT ] ,
        [ "prenom" => "Sylive" , "nom" => "Dutertre", "genre" => Utilisateur::GENRE_FEMME , "courriel" => "sylvie.dutertre@ad-clo.fr" , "motDePasse" => "SDazerty11!" , "telephoneFixe" => "+33 1 23 24 25 26" , "telephoneMobile" => "+33 6 23 24 25 26" , "mediaDeContact" => "SMS" , "numeroEtRue" => "17 rue des Papillons" , "codePostal" => "74011" , "ville" => "Thiais" , "societe" => "SESA" , "role" => Utilisateur::ROLE_CLIENT ]
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
            'mediasDeContact' => [ $u[ "mediaDeContact" ] ] ,
            'dateHeureInsertion' => new \DateTimeImmutable( 'now', new \DateTimeZone('Europe/Paris') ) ,
            'genre' => $u[ "genre" ] ,
            'nom' => $u[ "nom" ] ,
            'password' => $this->hasher->hashPassword( $utilisateur , $u[ "motDePasse" ] ) ,
            // 'password' => $u[ "motDePasse" ] ,
            'prenom' => $u[ "prenom" ] ,
            'roles' => [ $u[ "role" ] ] ,
            'rueEtNumero' => $u[ "numeroEtRue" ] ,
            'societe' => $u[ "societe" ] ,
            'telephoneFixe' => $u[ "telephoneFixe" ] ,
            'telephoneMobile' => $u[ "telephoneMobile" ] ,
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
