<?php

namespace App\Entity;

use App\Repository\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numeroContrat = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebutPrevue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cheminFichier = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureInsertion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureMAJ = null;

    /**
     * @var Collection<int, EtatContrat>
     */
    #[ORM\OneToMany(targetEntity: EtatContrat::class, mappedBy: 'idContrat', orphanRemoval: true, cascade: ['persist' , 'remove'])]
    private Collection $etatsContrat;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $idUtilisateur = null;

    /**
     * @var Collection<int, Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'idContrat', orphanRemoval: true)]
    private Collection $publications;

    public function __construct()
    {
        $this->etatsContrat = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function getNumeroContrat(): ?string
    {
        return $this->numeroContrat;
    }

    public function setNumeroContrat(string $numeroContrat): static
    {
        $this->numeroContrat = $numeroContrat;

        return $this;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebutPrevue(): ?\DateTimeInterface
    {
        return $this->dateDebutPrevue;
    }

    public function setDateDebutPrevue(?\DateTimeInterface $dateDebutPrevue): static
    {
        $this->dateDebutPrevue = $dateDebutPrevue;

        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(?\DateTimeInterface $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getCheminFichier(): ?string
    {
        return $this->cheminFichier;
    }

    public function setCheminFichier(?string $cheminFichier): static
    {
        $this->cheminFichier = $cheminFichier;

        return $this;
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

    /**
     * @return Collection<int, EtatContrat>
     */
    public function getEtatsContrat(): Collection
    {
        return $this->etatsContrat;
    }

    public function addEtatContrat(EtatContrat $etatContrat): static
    {
        if (!$this->etatsContrat->contains($etatContrat)) {
            $this->etatsContrat->add($etatContrat);
            $etatContrat->setIdContrat($this);
        }

        return $this;
    }

    public function removeEtatContrat(EtatContrat $etatContrat): static
    {
        if ($this->etatsContrat->removeElement($etatContrat)) {
            // set the owning side to null (unless already changed)
            if ($etatContrat->getIdContrat() === $this) {
                $etatContrat->setIdContrat(null);
            }
        }

        return $this;
    }

    public function getDernierEtat(): ?EtatContrat
    {
        return $this->etatsContrat->reduce(function ($plusJeune, $etat) {
            if ($plusJeune === null) {
                return $etat;
            }

            return $etat->getDateHeureInsertion() > $plusJeune->getDateHeureInsertion()
                ? $etat
                : $plusJeune;
        }, null);
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

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setIdContrat($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getIdContrat() === $this) {
                $publication->setIdContrat(null);
            }
        }

        return $this;
    }

}
