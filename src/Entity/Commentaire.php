<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $texte = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureInsertion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureMAJ = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'idCommentaire')]
    private Collection $photos;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $idPublication = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'commentaires')]
    private ?self $idCommentaireParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'idCommentaireParent')]
    private Collection $commentaires;

    #[ORM\OneToOne(inversedBy: 'commentaires', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $idUtilisateur = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): static
    {
        $this->texte = $texte;

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
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setIdCommentaire($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getIdCommentaire() === $this) {
                $photo->setIdCommentaire(null);
            }
        }

        return $this;
    }

    public function getIdPublication(): ?Publication
    {
        return $this->idPublication;
    }

    public function setIdPublication(?Publication $idPublication): static
    {
        $this->idPublication = $idPublication;

        return $this;
    }

    public function getIdCommentaireParent(): ?self
    {
        return $this->idCommentaireParent;
    }

    public function setIdCommentaireParent(?self $idCommentaireParent): static
    {
        $this->idCommentaireParent = $idCommentaireParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(self $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setIdCommentaireParent($this);
        }

        return $this;
    }

    public function removeCommentaire(self $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getIdCommentaireParent() === $this) {
                $commentaire->setIdCommentaireParent(null);
            }
        }

        return $this;
    }

    public function getIdUtilisateur(): ?Utilisateur
    {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur(Utilisateur $idUtilisateur): static
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }
}
