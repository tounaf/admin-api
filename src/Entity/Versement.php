<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use App\Repository\VersementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VersementRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['versem:read']]),
        new Post(denormalizationContext: ['groups' => ['versem:write']])
    ]
)]
class Versement
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['versem:read', 'sabbat:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 20)] // 'CASH' ou 'MOBILE_MONEY'
    #[Groups(['versem:read', 'versem:write', 'sabbat:read'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['versem:read', 'versem:write', 'sabbat:read'])]
    private ?float $montant = null;

    #[ORM\Column(length: 100, nullable: true)] // Réf SMS ou Nom du porteur
    #[Groups(['versem:read', 'versem:write', 'sabbat:read'])]
    private ?string $reference = null;

    #[ORM\Column(type: 'float', nullable: true)] // Frais d'envoi si Mobile Money
    #[Groups(['versem:read', 'versem:write', 'sabbat:read'])]
    private ?float $frais = 0.0;

    #[ORM\OneToOne(inversedBy: 'versement', targetEntity: SabbatValidation::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['versem:read', 'versem:write'])]
    private ?SabbatValidation $sabbatValidation = null;

    #[ORM\Column]
    #[Groups(['versem:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }

    // Getters & Setters...
    public function getId(): ?int { return $this->id; }
    public function getType(): ?string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getMontant(): ?float { return $this->montant; }
    public function setMontant(float $montant): self { $this->montant = $montant; return $this; }
    public function getReference(): ?string { return $this->reference; }
    public function setReference(?string $reference): self { $this->reference = $reference; return $this; }
    public function getFrais(): ?float { return $this->frais; }
    public function setFrais(?float $frais): self { $this->frais = $frais; return $this; }
    public function getSabbatValidation(): ?SabbatValidation { return $this->sabbatValidation; }
    public function setSabbatValidation(?SabbatValidation $sabbatValidation): self { $this->sabbatValidation = $sabbatValidation; return $this; }
}