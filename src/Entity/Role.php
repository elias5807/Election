<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types; // <--- Import indispensable pour corriger l'erreur de type
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    // ======================================================
    // 1. CONFIGURATION DE L'ID (Clé primaire)
    // ======================================================
    #[ORM\Id]
    #[ORM\GeneratedValue]
    // On précise explicitement le nom de la colonne SQL et son type
    #[ORM\Column(name: 'id_role', type: Types::INTEGER)]
    private ?int $id = null;

    // ======================================================
    // 2. LES AUTRES COLONNES
    // ======================================================
    
    #[ORM\Column(length: 50)]
    private ?string $libelle = null; // Ex: Administrateur

    #[ORM\Column(length: 50, name: 'role_string')]
    private ?string $roleString = null; // Ex: ROLE_ADMIN

    // Relation Inverse : Un Role a plusieurs Respos
    #[ORM\OneToMany(mappedBy: 'role', targetEntity: Respo::class)]
    private Collection $respos;

    public function __construct()
    {
        $this->respos = new ArrayCollection();
    }

    // ======================================================
    // 3. GETTERS ET SETTERS
    // ======================================================

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getRoleString(): ?string
    {
        return $this->roleString;
    }

    public function setRoleString(string $roleString): static
    {
        $this->roleString = $roleString;
        return $this;
    }

    /**
     * @return Collection<int, Respo>
     */
    public function getRespos(): Collection
    {
        return $this->respos;
    }
}