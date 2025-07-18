<?php

namespace App\Entity;

use App\Repository\FiangonanaRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FiangonanaRepository::class)]
#[ApiResource(
    operations: [
        new Get,
        new Post(),
        new Put(),
        new Delete(),
        new Patch(),
        new GetCollection(uriTemplate: '/fiangonanas{._format}', filters: ['app.fiangonana_search_filter'])
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
class Fiangonana
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['read', 'write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $adresse = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?float $longitude = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $code = null;

    /**
     * @var Collection<int, Offering>
     */
    #[ORM\OneToMany(targetEntity: Offering::class, mappedBy: 'fiangonana')]
    private Collection $offerings;

    /**
     * @var Collection<int, Expense>
     */
    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'fiangonana', orphanRemoval: true)]
    private Collection $expenses;

    public function __construct()
    {
        $this->offerings = new ArrayCollection();
        $this->expenses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, Offering>
     */
    public function getOfferings(): Collection
    {
        return $this->offerings;
    }

    public function addOffering(Offering $offering): static
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->setFiangonana($this);
        }

        return $this;
    }

    public function removeOffering(Offering $offering): static
    {
        if ($this->offerings->removeElement($offering)) {
            // set the owning side to null (unless already changed)
            if ($offering->getFiangonana() === $this) {
                $offering->setFiangonana(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Expense>
     */
    public function getExpenses(): Collection
    {
        return $this->expenses;
    }

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setFiangonana($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            // set the owning side to null (unless already changed)
            if ($expense->getFiangonana() === $this) {
                $expense->setFiangonana(null);
            }
        }

        return $this;
    }
}
