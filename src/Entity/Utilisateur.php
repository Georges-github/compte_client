<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_COURRIEL', fields: ['courriel'])]
#[UniqueEntity(fields: ['courriel'], message: 'There is already an account with this courriel')]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const GENRE_HOMME = 'Homme';
    public const GENRE_FEMME = 'Femme';

    public const ROLE_UTILISATEUR = 'ROLE_UTILISATEUR';
    
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const ROLE_EMPLOYE = 'ROLE_EMPLOYE';
    public const ROLE_ANCIEN_EMPLOYE = 'ROLE_ANCIEN_EMPLOYE';

    public const ROLE_CLIENT_POTENTIEL = 'ROLE_CLIENT_POTENTIEL';
    public const ROLE_CLIENT_POTENTIEL_ABANDON = 'ROLE_CLIENT_POTENTIEL_ABANDON';
    public const ROLE_CLIENT = 'ROLE_CLIENT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Email()]
    #[Assert\NotBlank()]
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
    #[Assert\NotCompromisedPassword()]
    #[Assert\PasswordStrength(minScore: Assert\PasswordStrength::STRENGTH_STRONG)]
    #[Assert\Regex('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*\W)(?!.*\s).{8,32}$/')]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 20)]
    private ?string $genre = null;

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

    #[ORM\Column]
    private bool $isVerified = false;

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
        $roles[] = 'ROLE_UTILISATEUR';

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

    public static function getLesRoles(): array
    {
        return [
            self::ROLE_UTILISATEUR => 'ROLE_UTILISATEUR' ,

            self::ROLE_ADMIN => 'ROLE_ADMIN' ,

            self::ROLE_EMPLOYE => 'ROLE_EMPLOYE' ,
            self::ROLE_ANCIEN_EMPLOYE => 'ROLE_ANCIEN_EMPLOYE' ,
        
            self::ROLE_CLIENT_POTENTIEL => 'ROLE_CLIENT_POTENTIEL' ,
            self::ROLE_CLIENT_POTENTIEL_ABANDON => 'ROLE_CLIENT_POTENTIEL_ABANDON' ,
            self::ROLE_CLIENT => 'ROLE_CLIENT'
        ];
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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public static function getGenres(): array
    {
        return [
            self::GENRE_HOMME => 'Homme' ,
            self::GENRE_FEMME => 'Femme'
        ];
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

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

}
