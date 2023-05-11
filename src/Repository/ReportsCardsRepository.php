<?php

namespace App\Repository;

use App\Entity\ReportsCards;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ReportsCards|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReportsCards|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReportsCards[]    findAll()
 * @method ReportsCards[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsCardsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReportsCards::class);
    }

    // /**
    //  * @return ReportsCards[] Returns an array of ReportsCards objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReportsCards
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
