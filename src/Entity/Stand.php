<?php

namespace App\Entity;

use App\Repository\StandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandRepository::class)]
#[ORM\Table(name: 'stand')]
class Stand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_stand')] // Mapping sur votre colonne SQL spécifique
    private ?int $id = null;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $crepe = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $lait = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $oeuf = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $rhum = false;

    #[ORM\Column(options: ['default' => false])]
    private ?bool $farine = false;

    // Relation avec POLE
    // On suppose qu'un Pôle peut avoir plusieurs stands (ou un seul), 
    // mais techniquement c'est Stand qui porte la clé étrangère.
    #[ORM\OneToOne(targetEntity: Pole::class)]
    #[ORM\JoinColumn(name: 'id_pole', referencedColumnName: 'id_pole', nullable: true)]
    private ?Pole $pole = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isCrepe(): ?bool
    {
        return $this->crepe;
    }

    public function setCrepe(bool $crepe): static
    {
        $this->crepe = $crepe;

        return $this;
    }

    public function isLait(): ?bool
    {
        return $this->lait;
    }

    public function setLait(bool $lait): static
    {
        $this->lait = $lait;

        return $this;
    }

    public function isOeuf(): ?bool
    {
        return $this->oeuf;
    }

    public function setOeuf(bool $oeuf): static
    {
        $this->oeuf = $oeuf;

        return $this;
    }

    public function isRhum(): ?bool
    {
        return $this->rhum;
    }

    public function setRhum(bool $rhum): static
    {
        $this->rhum = $rhum;

        return $this;
    }

    public function isFarine(): ?bool
    {
        return $this->farine;
    }

    public function setFarine(bool $farine): static
    {
        $this->farine = $farine;

        return $this;
    }

    public function getPole(): ?Pole
    {
        return $this->pole;
    }

    public function setPole(?Pole $pole): static
    {
        $this->pole = $pole;

        return $this;
    }
}