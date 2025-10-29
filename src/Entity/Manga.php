<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $serie = null;

    #[ORM\Column(nullable: true)]
    private ?int $tome = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Bibliotheque $bibliotheque = null;

    /**
     * @var Collection<int, Vitrine>
     */
    #[ORM\ManyToMany(targetEntity: Vitrine::class, mappedBy: 'mangas')]
    private Collection $vitrines;

    public function __construct()
    {
        $this->vitrines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): static
    {
        $this->serie = $serie;
        $this->syncTitre();
        return $this;
    }

    public function getTome(): ?int
    {
        return $this->tome;
    }

    public function setTome(?int $tome): static
    {
        $this->tome = $tome;
        $this->syncTitre();
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Titre est dérivé de (serie, tome). Laisser public au cas où,
     * mais il sera toujours recalculé via syncTitre() / callbacks.
     */
    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getBibliotheque(): ?Bibliotheque
    {
        return $this->bibliotheque;
    }

    public function setBibliotheque(?Bibliotheque $bibliotheque): static
    {
        $this->bibliotheque = $bibliotheque;
        return $this;
    }

    /**
     * @return Collection<int, Vitrine>
     */
    public function getVitrines(): Collection
    {
        return $this->vitrines;
    }

    public function addVitrine(Vitrine $vitrine): static
    {
        if (!$this->vitrines->contains($vitrine)) {
            $this->vitrines->add($vitrine);
            $vitrine->addManga($this);
        }
        return $this;
    }

    public function removeVitrine(Vitrine $vitrine): static
    {
        if ($this->vitrines->removeElement($vitrine)) {
            $vitrine->removeManga($this);
        }
        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function onPreSave(): void
    {
        $this->syncTitre();
    }

    private function syncTitre(): void
    {
        $serie = $this->serie ? trim($this->serie) : '';
        if ($serie !== '' && $this->tome !== null) {
            $this->titre = $serie . ' — tome ' . (int) $this->tome;
        } else {
            $this->titre = $serie !== '' ? $serie : null;
        }
    }
}
