<?php

namespace App\Factory;

use App\Entity\EtatContrat;
use App\Enum\EnumEtatContrat;
use App\Repository\EtatContratRepository;
use Doctrine\ORM\EntityRepository;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\ProxyRepositoryDecorator;

/**
 * @extends PersistentProxyObjectFactory<EtatContrat>
 */
final class EtatContratFactory extends PersistentProxyObjectFactory{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return EtatContrat::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'dateHeureInsertion' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'etat' => self::faker()->randomElement(EnumEtatContrat::cases()),
            'idContrat' => ContratFactory::new(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(EtatContrat $etatContrat): void {})
        ;
    }
}
