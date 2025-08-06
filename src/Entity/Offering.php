<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\OfferingRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: OfferingRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['offering:read']],
    denormalizationContext: ['groups' => ['offering:write']]
)]
#[ApiFilter(SearchFilter::class, properties: ['fiangonana' => 'exact'])]
#[ApiFilter(DateFilter::class, properties: ['date'])]
class Offering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['offering:read','offering:write'])]
    private ?string $type = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['offering:read','offering:write'])]
    private ?array $quantities = null;

    #[ORM\Column]
    #[Groups(['offering:read','offering:write'])]
    private ?float $total = null;

    #[Groups(['offering:read','offering:write'])]
    #[ORM\ManyToOne(inversedBy: 'offerings')]
    private ?Fiangonana $fiangonana = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['offering:read'])]
    private ?\DateTime $date = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->date = new \DateTime();
    }

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }
}
