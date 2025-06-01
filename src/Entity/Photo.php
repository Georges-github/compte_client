<?php

namespace App\Entity;

use App\Repository\PhotoRepository;

use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $legende = null;

    #[ORM\Column(length: 500)]
    private ?string $cheminFichierImage = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $dateHeureInsertion = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateHeureMAJ = null;

    // #[ORM\ManyToOne(inversedBy: 'photos')]
    // #[ORM\JoinColumn(nullable: true)]
    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Publication $idPublication = null;

    // #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\ManyToOne(inversedBy: 'photos')]
    #[ORM\JoinColumn(nullable: true, onDelete: "CASCADE")]
    private ?Commentaire $idCommentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLegende(): ?string
    {
        return $this->legende;
    }

    public function setLegende(?string $legende): static
    {
        $this->legende = $legende;

        return $this;
    }

    public function getCheminFichierImage(): ?string
    {
        return $this->cheminFichierImage;
    }

    public function setCheminFichierImage(string $cheminFichierImage): static
    {
        $this->cheminFichierImage = $cheminFichierImage;

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

    public function getIdPublication(): ?Publication
    {
        return $this->idPublication;
    }

    public function setIdPublication(?Publication $idPublication): static
    {
        $this->idPublication = $idPublication;

        return $this;
    }

    public function getIdCommentaire(): ?Commentaire
    {
        return $this->idCommentaire;
    }

    public function setIdCommentaire(?Commentaire $idCommentaire): static
    {
        $this->idCommentaire = $idCommentaire;

        return $this;
    }
}
