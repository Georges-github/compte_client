<?php

namespace App\Entity;

use App\Enum\Genre;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_COURRIEL', fields: ['courriel'])]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $courriel = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(enumType: Genre::class)]
    private ?Genre $genre = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $rueEtNumero = null;

    #[ORM\Column(length: 20)]
    private ?string $codePostal = null;

    #[ORM\Column(length: 100)]
    private ?string $ville = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $societe = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureInsertion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureMAJ = null;

    /**
     * @var Collection<int, EtatContrat>
     */
    #[ORM\OneToMany(targetEntity: EtatContrat::class, mappedBy: 'idUtilisateur')]
    private Collection $etatContrats;

    /**
     * @var Collection<int, Contrat>
     */
    #[ORM\OneToMany(targetEntity: Contrat::class, mappedBy: 'idUser', orphanRemoval: true)]
    private Collection $contrats;

    /**
     * @var Collection<int, Publication>
     */
    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'idUtilisateur')]
    private Collection $publications;

    public function __construct()
    {
        $this->etatContrats = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCourriel(): ?string
    {
        return $this->courriel;
    }

    public function setCourriel(string $courriel): static
    {
        $this->courriel = $courriel;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->courriel;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getRueEtNumero(): ?string
    {
        return $this->rueEtNumero;
    }

    public function setRueEtNumero(string $rueEtNumero): static
    {
        $this->rueEtNumero = $rueEtNumero;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(?string $societe): static
    {
        $this->societe = $societe;

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
    public function getEtatContrats(): Collection
    {
        return $this->etatContrats;
    }

    public function addEtatContrat(EtatContrat $etatContrat): static
    {
        if (!$this->etatContrats->contains($etatContrat)) {
            $this->etatContrats->add($etatContrat);
            $etatContrat->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeEtatContrat(EtatContrat $etatContrat): static
    {
        if ($this->etatContrats->removeElement($etatContrat)) {
            // set the owning side to null (unless already changed)
            if ($etatContrat->getIdUtilisateur() === $this) {
                $etatContrat->setIdUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getIdUtilisateur() === $this) {
                $contrat->setIdUtilisateur(null);
            }
        }

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
            $publication->setIdUtilisateur($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getIdUtilisateur() === $this) {
                $publication->setIdUtilisateur(null);
            }
        }

        return $this;
    }

}
