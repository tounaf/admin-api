<?php

// src/Controller/ExpenseTotalByFiangonanaController.php
namespace App\Controller;

use App\Dto\ExpenseTotalByFiangonana;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ExpenseTotalByFiangonanaController
{
    
    public function __construct(private ExpenseRepository $expenseRepository)
    {
        
    }
    public function __invoke(): JsonResponse
    {
        // $conn = $this->entityManager->getConnection();
        // $sql = "
        //     SELECT fiangonana AS fiangonanaName, SUM(amount) AS totalAmount
        //     FROM expense
        //     GROUP BY fiangonana
        //     ORDER BY totalAmount DESC
        // ";

        // $stmt = $conn->prepare($sql);
        // $results = $stmt->executeQuery()->fetchAllAssociative();

        // return new JsonResponse($results);

        $results = $this->expenseRepository->findExpenseByFiangonanaAndDate();
        $dtos = [];

        foreach ($results as $result) {
            $dto = new ExpenseTotalByFiangonana();
            $dto->fiangonanaName = $result['fiangonana_name'];
            $dto->totalAmount = (float) $result['total_offering'];
            // Format the DateTime object as a string
            // $dto->date = $result['date'] instanceof \DateTime ? $result['date']->format('Y-m-d') : null;
            $dtos[] = $dto;
        }

        return new JsonResponse($dtos);

    }
}
