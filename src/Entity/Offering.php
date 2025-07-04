<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\OfferingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferingRepository::class)]
#[ApiResource]
class Offering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    private ?array $quantities = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\ManyToOne(inversedBy: 'offerings')]
    private ?Fiangonana $fiangonana = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getQuantities(): ?array
    {
        return $this->quantities;
    }

    public function setQuantities(?array $quantities): static
    {
        $this->quantities = $quantities;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getFiangonana(): ?Fiangonana
    {
        return $this->fiangonana;
    }

    public function setFiangonana(?Fiangonana $fiangonana): static
    {
        $this->fiangonana = $fiangonana;

        return $this;
    }
}
