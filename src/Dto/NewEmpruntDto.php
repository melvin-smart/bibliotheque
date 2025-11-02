<?php

namespace App\Dto;

use App\Entity\Livre;
use Symfony\Component\Validator\Constraint as Assert;

class NewEmpruntDto
{
    public ?string $nom = null;

    public ?string $prenom = null;

    public ?string $email = null;

    public ?string $phone = null;

    public ?\DateTimeImmutable $date_inscription = null;

    public ?string $adresse = null;

    public ?Livre $livre = null;

    public ?\DateTimeImmutable $date_emprunt = null;

    public ?\DateTimeImmutable $date_retour = null;

    public ?string $statut = null;

    public function setDateInscription(\DateTimeImmutable $date_inscription): static
    {
        $this->date_inscription = $date_inscription;
        return $this;
    }

    public function setDateEmprunt(\DateTimeImmutable $date_emprunt): static
    {
        $this->date_emprunt = $date_emprunt;
        return $this;
    }

    public function setDateRetour(\DateTimeImmutable $date_retour): static
    {
        $this->date_retour = $date_retour;
        return $this;
    }
}
