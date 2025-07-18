<?php
// src/Dto/ExpenseBatchInput.php
namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

class ExpenseBatchInput
{
    #[Groups(['write'])]
    public array $expenses;

    public function __construct(array $expenses = [])
    {
        $this->expenses = $expenses;
    }
}