<?php

namespace App\Entity;

use App\Repository\EtatContratRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatContratRepository::class)]
class EtatContrat
{

    public const ETAT_EN_DISCUSSION = 'En discussion';
    public const ETAT_A_VENIR = 'A venir';
    public const ETAT_EN_COURS = 'En cours';
    public const ETAT_EN_PAUSE = 'En pause';
    public const ETAT_ANNULE = 'Annulé';
    public const ETAT_TERMINE = 'Terminé';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $etat = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureInsertion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureMAJ = null;

    #[ORM\ManyToOne(inversedBy: 'etatsContrat')]
    private ?Utilisateur $idUtilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'etatsContrat')]
    #[ORM\JoinColumn(nullable: false , onDelete: 'CASCADE')]
    private ?Contrat $idContrat = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): static
    {
        $this->etat = $etat;

        return $this;
    }

    public static function getLesEtats(): array
    {
        return [
            self::ETAT_EN_DISCUSSION => 'En discussion' ,
            self::ETAT_A_VENIR => 'A venir' ,
            self::ETAT_EN_COURS => 'En cours' ,
            self::ETAT_EN_PAUSE => 'En pause' ,
            self::ETAT_ANNULE => 'Annulé' ,
            self::ETAT_TERMINE => 'Terminé'
        ];
    }

    public function getDateHeureInsertion(): ?\DateTimeImmutable
    {
        return $this->dateHeureInsertion;
    }

    public function setDateHeureInsertion(\DateTimeImmutable $dateHeureInsertion): static
    {
        $this->dateHeureInsertion = $dateHeureInsertion;

        return $this;
    }

    public function getDateHeureMAJ(): ?\DateTimeInterface
    {
        return $this->dateHeureMAJ;
    }

    public function setDateHeureMAJ(?\DateTimeInterface $dateHeureMAJ): static
    {
        $this->dateHeureMAJ = $dateHeureMAJ;

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(?Utilisateur $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    public function getIdContrat(): ?Contrat
    {
        return $this->idContrat;
    }

    public function setIdContrat(?Contrat $idContrat): static
    {
        $this->idContrat = $idContrat;

        return $this;
    }

}
