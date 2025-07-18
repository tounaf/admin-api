<?php
namespace App\Dto;

use App\Entity\Expense;
use Symfony\Component\Serializer\Annotation\Groups;

class ExpenseBatchOutput
{
    #[Groups(['read'])]
    public array $expenses = [];

    public function __construct(array $expenses)
    {
        $this->expenses = $expenses;
    }
}
