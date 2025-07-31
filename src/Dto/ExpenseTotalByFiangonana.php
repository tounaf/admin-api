<?php

// src/Dto/ExpenseTotalByFiangonana.php
namespace App\Dto;

use Symfony\Component\Serializer\Attribute\Groups;

class ExpenseTotalByFiangonana
{
    #[Groups(['expense:total_by_fiangonana'])]
    public string $fiangonanaName;
    #[Groups(['expense:total_by_fiangonana'])]
    public float $totalAmount;
}
