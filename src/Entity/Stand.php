<?php

namespace App\Entity;

use App\Repository\StandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StandRepository::class)]
class Stand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $crepe = null;

    #[ORM\Column]
    private ?bool $lait = null;

    #[ORM\Column]
    private ?bool $oeuf = null;

    #[ORM\Column]
    private ?bool $rhum = null;

    #[ORM\Column]
    private ?bool $farine = null;

    // Pour #id_stand_1 (Relation sur soi-même, optionnelle)
    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parentStand = null;

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

    public function getParentStand(): ?self
    {
        return $this->parentStand;
    }

    public function setParentStand(?self $parentStand): static
    {
        $this->parentStand = $parentStand;
        return $this;
    }
}