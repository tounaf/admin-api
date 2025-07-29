<?php

namespace App\Controller;

use App\Dto\OfferingTotalByFiangonana;
use App\Repository\OfferingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class OfferingTotalByFiangonanaController extends AbstractController
{
    private $offeringRepository;

    public function __construct(OfferingRepository $offeringRepository)
    {
        $this->offeringRepository = $offeringRepository;
    }

    public function __invoke()
    {
        $results = $this->offeringRepository->findTotalByFiangonanaAndDate();
        $dtos = [];

        foreach ($results as $result) {
            $dto = new OfferingTotalByFiangonana();
            $dto->fiangonanaName = $result['fiangonana_name'];
            $dto->totalOffering = (float) $result['total_offering'];
            // Format the DateTime object as a string
            $dto->date = $result['date'] instanceof \DateTime ? $result['date']->format('Y-m-d') : null;
            $dtos[] = $dto;
        }

        return new JsonResponse($dtos);
    }
}