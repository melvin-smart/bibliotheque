<?php

namespace App\Entity;

use App\Repository\EmpruntRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmpruntRepository::class)]
class Emprunt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_emprunt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_retour = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_retour_reelle = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $emprunteur = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    private ?User $bibliothecaire = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $date_validation = null;

    #[ORM\ManyToOne(inversedBy: 'emprunts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Livre $livre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDateEmprunt(): ?\DateTimeImmutable
    {
        return $this->date_emprunt;
    }

    public function setDateEmprunt(\DateTimeImmutable $date_emprunt): static
    {
        $this->date_emprunt = $date_emprunt;

        return $this;
    }

    public function getDateRetour(): ?\DateTimeImmutable
    {
        return $this->date_retour;
    }

    public function setDateRetour(\DateTimeImmutable $date_retour): static
    {
        $this->date_retour = $date_retour;

        return $this;
    }

    public function getDateRetourReelle(): ?\DateTimeImmutable
    {
        return $this->date_retour_reelle;
    }

    public function setDateRetourReelle(?\DateTimeImmutable $date_retour_reelle): static
    {
        $this->date_retour_reelle = $date_retour_reelle;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getEmprunteur(): ?User
    {
        return $this->emprunteur;
    }

    public function setEmprunteur(?User $emprunteur): static
    {
        $this->emprunteur = $emprunteur;

        return $this;
    }

    public function getBibliothecaire(): ?User
    {
        return $this->bibliothecaire;
    }

    public function setBibliothecaire(?User $bibliothecaire): static
    {
        $this->bibliothecaire = $bibliothecaire;

        return $this;
    }

    public function getDateValidation(): ?\DateTimeImmutable
    {
        return $this->date_validation;
    }

    public function setDateValidation(?\DateTimeImmutable $date_validation): static
    {
        $this->date_validation = $date_validation;

        return $this;
    }

    public function getLivre(): ?Livre
    {
        return $this->livre;
    }

    public function setLivre(?Livre $livre): static
    {
        $this->livre = $livre;

        return $this;
    }
}
