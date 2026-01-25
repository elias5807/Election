<?php

namespace App\Entity;

use App\Repository\MilitantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MilitantRepository::class)]
#[ORM\Table(name: 'militant')]
class Militant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_militant', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $asso = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $campusEtude = null;

    #[ORM\Column(length: 50)]
    private ?string $mail = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $regimeAlimentaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $niveauTractage = null;

    #[ORM\Column]
    private ?bool $orateur = null;

    #[ORM\Column]
    private ?bool $debat = null;

    // Relation pour la table "Horaire" (Un militant a plusieurs horaires)
    #[ORM\OneToMany(mappedBy: 'militant', targetEntity: Horaire::class)]
    private Collection $horaires;

    #[ORM\ManyToOne(targetEntity: Pole::class, inversedBy: 'militants')]
    #[ORM\JoinColumn(
        name: 'id_pole',             // Nom de la colonne dans la table "Militant"
        referencedColumnName: 'id_pole', // <--- TRES IMPORTANT : Nom de la colonne ID dans la table "Pole"
        nullable: true
    )]
    private ?Pole $pole = null;

    public function __construct()
    {
        $this->horaires = new ArrayCollection();
        $this->poles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAsso(): ?string
    {
        return $this->asso;
    }

    public function setAsso(?string $asso): static
    {
        $this->asso = $asso;
        return $this;
    }

    public function getCampusEtude(): ?string
    {
        return $this->campusEtude;
    }

    public function setCampusEtude(?string $campusEtude): static
    {
        $this->campusEtude = $campusEtude;
        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;
        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): static
    {
        $this->tel = $tel;
        return $this;
    }

    public function getRegimeAlimentaire(): ?string
    {
        return $this->regimeAlimentaire;
    }

    public function setRegimeAlimentaire(?string $regimeAlimentaire): static
    {
        $this->regimeAlimentaire = $regimeAlimentaire;
        return $this;
    }

    public function getNiveauTractage(): ?int
    {
        return $this->niveauTractage;
    }

    public function setNiveauTractage(?int $niveauTractage): static
    {
        $this->niveauTractage = $niveauTractage;
        return $this;
    }

    public function isOrateur(): ?bool
    {
        return $this->orateur;
    }

    public function setOrateur(bool $orateur): static
    {
        $this->orateur = $orateur;
        return $this;
    }

    public function isDebat(): ?bool
    {
        return $this->debat;
    }

    public function setDebat(bool $debat): static
    {
        $this->debat = $debat;
        return $this;
    }

    /**
     * @return Collection<int, Horaire>
     */
    public function getHoraires(): Collection
    {
        return $this->horaires;
    }

    public function addHoraire(Horaire $horaire): static
    {
        if (!$this->horaires->contains($horaire)) {
            $this->horaires->add($horaire);
            $horaire->setMilitant($this);
        }
        return $this;
    }

    public function removeHoraire(Horaire $horaire): static
    {
        if ($this->horaires->removeElement($horaire)) {
            // set the owning side to null (unless already changed)
            if ($horaire->getMilitant() === $this) {
                $horaire->setMilitant(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Pole>
     */
    public function getPole(): ?Pole
    {
        return $this->pole;
    }

    public function setPole(?Pole $pole): static
    {
        $this->pole = $pole;
        return $this;
    }

    public function addPole(Pole $pole): static
    {
        if (!$this->poles->contains($pole)) {
            $this->poles->add($pole);
        }
        return $this;
    }

    public function removePole(Pole $pole): static
    {
        $this->poles->removeElement($pole);
        return $this;
    }

}