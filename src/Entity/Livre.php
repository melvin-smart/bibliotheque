<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivreRepository::class)]
class Livre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'livres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genre $genre = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $auteur = null;

    #[ORM\Column(length: 255)]
    private ?string $isbn = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_publication = null;

    #[ORM\Column]
    private ?int $qte_totale = null;

    #[ORM\Column]
    private ?int $qte_dispo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, ImgLivre>
     */
    #[ORM\OneToMany(targetEntity: ImgLivre::class, mappedBy: 'livre', orphanRemoval: true)]
    private Collection $imgLivres;

    public function __construct()
    {
        $this->imgLivres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeImmutable
    {
        return $this->date_publication;
    }

    public function setDatePublication(\DateTimeImmutable $date_publication): static
    {
        $this->date_publication = $date_publication;

        return $this;
    }

    public function getQteTotale(): ?int
    {
        return $this->qte_totale;
    }

    public function setQteTotale(int $qte_totale): static
    {
        $this->qte_totale = $qte_totale;

        return $this;
    }

    public function getQteDispo(): ?int
    {
        return $this->qte_dispo;
    }

    public function setQteDispo(int $qte_dispo): static
    {
        $this->qte_dispo = $qte_dispo;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, ImgLivre>
     */
    public function getImgLivres(): Collection
    {
        return $this->imgLivres;
    }

    public function addImgLivre(ImgLivre $imgLivre): static
    {
        if (!$this->imgLivres->contains($imgLivre)) {
            $this->imgLivres->add($imgLivre);
            $imgLivre->setLivre($this);
        }

        return $this;
    }

    public function removeImgLivre(ImgLivre $imgLivre): static
    {
        if ($this->imgLivres->removeElement($imgLivre)) {
            // set the owning side to null (unless already changed)
            if ($imgLivre->getLivre() === $this) {
                $imgLivre->setLivre(null);
            }
        }

        return $this;
    }
}
