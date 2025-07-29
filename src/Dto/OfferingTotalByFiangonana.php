<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class OfferingTotalByFiangonana
{
    #[Groups(['offering:total_by_fiangonana'])]
    public ?string $fiangonanaName = null;

    #[Groups(['offering:total_by_fiangonana'])]
    public ?float $totalOffering = null;

    #[Groups(['offering:total_by_fiangonana'])]
    public string $date;
}