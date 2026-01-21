<?php

namespace App\Entity;

use App\Repository\RespoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: RespoRepository::class)]
class Respo implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tel = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $mail = null;

    #[ORM\Column]
    private ?string $mdp = null;

    #[ORM\ManyToOne(inversedBy: 'respos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pole $pole = null;

    // --- LOGIQUE DE SÉCURITÉ ---

    /**
     * Identifiant visuel unique (ici l'email)
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->mail;
    }

    /**
     * Les rôles de l'utilisateur.
     * Important : on garantit que chaque utilisateur a au moins ROLE_USER
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_RESPO'];
        // Garantie que tout le monde a au moins ce rôle de base
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * Retourne le mot de passe hashé
     */
    public function getPassword(): ?string
    {
        return $this->mdp;
    }

    public function eraseCredentials(): void
    {
        // Utile si vous stockez des données sensibles temporaires sur l'objet
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