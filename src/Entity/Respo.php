<?php

namespace App\Entity;

use App\Repository\RespoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: RespoRepository::class)]
class Respo implements UserInterface, PasswordAuthenticatedUserInterface
{
    // 1. ID (id_respo)
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_respo', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 50, name: 'nom')]
    private ?string $nom = null;

    #[ORM\Column(length: 50, name: 'prenom')]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, nullable: true, name: 'tel')]
    private ?string $tel = null;

    #[ORM\Column(length: 180, unique: true, name: 'mail')]
    private ?string $mail = null;

    #[ORM\Column(name: 'mdp')]
    private ?string $mdp = null;

    // 2. RELATION POLE (id_pole)
    #[ORM\ManyToOne(inversedBy: 'respos')]
    #[ORM\JoinColumn(name: 'id_pole', referencedColumnName: 'id_pole', nullable: false)]
    private ?Pole $pole = null;

    // 3. RELATION ROLE (id_role) - C'est celle qui vous manquait !
    #[ORM\ManyToOne(inversedBy: 'respos')]
    #[ORM\JoinColumn(name: 'id_role', referencedColumnName: 'id_role', nullable: false)]
    private ?Role $role = null;

    // --- LOGIQUE DE SÉCURITÉ ---

    public function getRoles(): array
    {
        // Maintenant $this->role existe, donc ça ne plantera plus
        $roleString = $this->role?->getRoleString();

        $roles = [];
        if ($roleString) {
            $roles[] = $roleString;
        }

        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function eraseCredentials(): void
    {
    }

    // --- GETTERS & SETTERS CLASSIQUES ---

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getTel(): ?string { return $this->tel; }
    public function setTel(?string $tel): static { $this->tel = $tel; return $this; }

    public function getMail(): ?string { return $this->mail; }
    public function setMail(string $mail): static { $this->mail = $mail; return $this; }

    public function getMdp(): ?string { return $this->mdp; }
    public function setMdp(string $mdp): static { $this->mdp = $mdp; return $this; }

    public function getPole(): ?Pole { return $this->pole; }
    public function setPole(?Pole $pole): static { $this->pole = $pole; return $this; }
}