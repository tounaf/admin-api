<?php

namespace App\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;

#[ApiResource(
    shortName: "OfferingStatistics",
    uriTemplate: "/offering_statistics",
    operations: [new GetCollection()],
    paginationEnabled: false,
    security: "is_granted('PUBLIC_ACCESS')"
)]
final class OfferingStatistics
{
    public ?string $fiangonana = null;
    public ?string $date = null;
    public ?float $total = null;
}

