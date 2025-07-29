<?php

namespace App\Repository;

use App\Entity\Offering;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offering>
 */
class OfferingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offering::class);
    }

    public function findTotalByFiangonanaAndDate()
    {
        return $this->createQueryBuilder('o')
            ->select('f.nom AS fiangonana_name', 'SUM(o.total) AS total_offering', 'o.date')
            ->join('o.fiangonana', 'f')
            ->groupBy('f.id, o.date')
            ->orderBy('o.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Offering[] Returns an array of Offering objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Offering
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
