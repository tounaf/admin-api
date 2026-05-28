<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SabbatValidationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;

#[ORM\Entity(repositoryClass: SabbatValidationRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            uriTemplate: '/sabbat-validations',
            processor: \App\State\SabbatValidationProcessor::class
        ),
        new Patch(
            denormalizationContext: ['groups' => ['sabbat:update']],
            inputFormats: ['json' => ['application/merge-patch+json']],
        )
    ],
    normalizationContext: ['groups' => ['sabbat:read']],
    denormalizationContext: ['groups' => ['sabbat:write']],
    order: ['dateSabbat' => 'DESC'],
    paginationClientItemsPerPage: true
)]
#[ApiFilter(SearchFilter::class, properties: ['fiangonana' => 'exact', 'status' => 'exact'])] // Permet ?fiangonana=ID
#[ApiFilter(ExistsFilter::class, properties: ['versement'])]
class SabbatValidation
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['sabbat:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?string $imageName = null; // Nom du fichier sur le disque

    #[ORM\Column(type: 'date')]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?\DateTimeInterface $dateSabbat = null;

    #[ORM\Column(length: 20)]
    #[Groups(['sabbat:read', 'sabbat:update'])]
    private string $status = 'PENDING'; // PENDING, VALIDATED, REJECTED

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?Fiangonana $fiangonana = null;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $ambimbolaTeoAloha = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $volaMiditraAndroany = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $volaNivoaka = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $volaSisaEoAntanana = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $volaMiditraA = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $caution = 0.0;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(['sabbat:read', 'sabbat:write'])]
    private ?float $rar = 0.0;

    #[ORM\OneToOne(mappedBy: 'sabbatValidation', cascade: ['persist', 'remove'])]
    #[Groups(['sabbat:read'])]
    private ?Versement $versement = null;

    // Getters et Setters...
    public function getId(): ?int { return $this->id; }
    public function getImageName(): ?string { return $this->imageName; }
    public function setImageName(string $imageName): self { $this->imageName = $imageName; return $this; }
    public function getDateSabbat(): ?\DateTimeInterface { return $this->dateSabbat; }
    public function setDateSabbat(\DateTimeInterface $dateSabbat): self { $this->dateSabbat = $dateSabbat; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getFiangonana(): ?Fiangonana { return $this->fiangonana; }
    public function setFiangonana(?Fiangonana $fiangonana): self { $this->fiangonana = $fiangonana; return $this; }

    public function getAmbimbolaTeoAloha(): ?float { return $this->ambimbolaTeoAloha; }
    public function setAmbimbolaTeoAloha(?float $amount): self { $this->ambimbolaTeoAloha = $amount; return $this; }

    public function getVolaMiditraAndroany(): ?float { return $this->volaMiditraAndroany; }
    public function setVolaMiditraAndroany(?float $amount): self { $this->volaMiditraAndroany = $amount; return $this; }

    public function getVolaNivoaka(): ?float { return $this->volaNivoaka; }
    public function setVolaNivoaka(?float $amount): self { $this->volaNivoaka = $amount; return $this; }

    public function getVolaSisaEoAntanana(): ?float { return $this->volaSisaEoAntanana; }
    public function setVolaSisaEoAntanana(?float $amount): self { $this->volaSisaEoAntanana = $amount; return $this; }

    public function getVersement(): ?Versement { return $this->versement; }
    public function setVersement(?Versement $versement): self {
        $this->versement = $versement;
        return $this;
    }

    public function getVolaMiditraA(): ?float { return $this->volaMiditraA; }
    public function setVolaMiditraA(?float $amount): self { $this->volaMiditraA = $amount; return $this; }

    public function getCaution(): ?float { return $this->caution; }
    public function setCaution(?float $amount): self { $this->caution = $amount; return $this; }

    public function getRar(): ?float { return $this->rar; }
    public function setRar(?float $amount): self { $this->rar = $amount; return $this; }
}