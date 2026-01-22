<?php

namespace App\Entity;

use App\Repository\PoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PoleRepository::class)]
class Pole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_pole', type: 'integer')] // <-- Vérifiez cette ligne
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nomPole = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    private ?float $lattitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;

    #[ORM\Column(nullable: true)]
    private ?int $tract = null;

    #[ORM\Column(nullable: true)]
    private ?int $afluence = null;

    // J'ai gardé string car ton schéma disait VARCHAR, même si ça ressemble à des scores
    //changer le type string en INT
    #[ORM\Column(nullable: true)]
    private ?int $faep = null;

    #[ORM\Column(nullable: true)]
    private ?int $uni = null;

    #[ORM\Column(nullable: true)]
    private ?int $ue = null;

    #[ORM\Column(nullable: true)]
    private ?int $unef = null;

    // Relation inverse de "Respo" (Un pôle a plusieurs responsables)
    #[ORM\OneToMany(mappedBy: 'pole', targetEntity: Respo::class)]
    private Collection $respos;

    // Relation inverse de "Militant"
    #[ORM\ManyToMany(targetEntity: Militant::class, mappedBy: 'poles')]
    private Collection $militants;

    public function __construct()
    {
        $this->respos = new ArrayCollection();
        $this->militants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPole(): ?string
    {
        return $this->nomPole;
    }

    public function setNomPole(string $nomPole): static
    {
        $this->nomPole = $nomPole;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getLattitude(): ?float
    {
        return $this->lattitude;
    }

    public function setLattitude(?float $lattitude): static
    {
        $this->lattitude = $lattitude;
        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getTract(): ?int
    {
        return $this->tract;
    }

    public function setTract(?int $tract): static
    {
        $this->tract = $tract;
        return $this;
    }

    public function getAfluence(): ?int
    {
        return $this->afluence;
    }

    public function setAfluence(?int $afluence): static
    {
        $this->afluence = $afluence;
        return $this;
    }

    //changer le type string en INT
    // Je suppose que FAEP, UNI, etc sont des strings (noms de syndicats adverses ?)
    public function getFaep(): ?int
    {
        return $this->faep;
    }

    public function setFaep(?int $faep): static
    {
        $this->faep = $faep;
        return $this;
    }

    public function getUni(): ?int
    {
        return $this->uni;
    }

    public function setUni(?int $uni): static
    {
        $this->uni = $uni;
        return $this;
    }

    public function getUe(): ?int
    {
        return $this->ue;
    }

    public function setUe(?int $ue): static
    {
        $this->ue = $ue;
        return $this;
    }

    public function getUnef(): ?int
    {
        return $this->unef;
    }

    public function setUnef(?int $unef): static
    {
        $this->unef = $unef;
        return $this;
    }

    /**
     * @return Collection<int, Respo>
     */
    public function getRespos(): Collection
    {
        return $this->respos;
    }

    public function addRespo(Respo $respo): static
    {
        if (!$this->respos->contains($respo)) {
            $this->respos->add($respo);
            $respo->setPole($this);
        }
        return $this;
    }

    public function removeRespo(Respo $respo): static
    {
        if ($this->respos->removeElement($respo)) {
            // set the owning side to null (unless already changed)
            if ($respo->getPole() === $this) {
                $respo->setPole(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Militant>
     */
    public function getMilitants(): Collection
    {
        return $this->militants;
    }

    public function addMilitant(Militant $militant): static
    {
        if (!$this->militants->contains($militant)) {
            $this->militants->add($militant);
            $militant->addPole($this);
        }
        return $this;
    }

    public function removeMilitant(Militant $militant): static
    {
        if ($this->militants->removeElement($militant)) {
            $militant->removePole($this);
        }
        return $this;
    }
}