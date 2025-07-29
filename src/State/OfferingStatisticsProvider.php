<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\OfferingStatistics;
use Doctrine\ORM\EntityManagerInterface;

final class OfferingStatisticsProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        try {
            $conn = $this->em->getConnection();
            $sql = "SELECT f.name as fiangonana, DATE(o.date) as date, SUM(o.total) as total
                    FROM offering o
                    JOIN fiangonana f ON f.id = o.fiangonana_id
                    GROUP BY f.name, DATE(o.date)
                    ORDER BY DATE(o.date), f.name";
            $rows = $conn->executeQuery($sql)->fetchAllAssociative();

            foreach ($rows as $row) {
                $stat = new OfferingStatistics();
                $stat->fiangonana = $row['fiangonana'];
                $stat->date = $row['date'];
                $stat->total = (float) $row['total'];
                yield $stat;
            }
        } catch (\Throwable $e) {
            // Log l’erreur ou faire un dump pour debug
            dump($e->getMessage());
            yield from [];
        }
    }

}
