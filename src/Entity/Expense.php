<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ExpenseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(
            uriTemplate: '/expenses',
            processor: \App\State\ExpenseBatchProcessor::class
        ),
        new Post(
            uriTemplate: '/expenses/batch',
            input: \App\Dto\ExpenseBatchInput::class,
            // output: ExpenseBatchOutput::class,
            processor: \App\State\ExpenseBatchProcessor::class,
            normalizationContext: ['groups' => ['read']] // Ajout explicite pour batch POST
        ),
        new GetCollection(uriTemplate: '/expenses{._format}')
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?int $amount = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['read', 'write'])]
    private ?\DateTime $dateSabbat = null;

    #[ORM\ManyToOne(inversedBy: 'expenses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Fiangonana $fiangonana = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDateSabbat(): ?\DateTime
    {
        return $this->dateSabbat;
    }

    public function setDateSabbat(\DateTime $dateSabbat): static
    {
        $this->dateSabbat = $dateSabbat;

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
