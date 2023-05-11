<?php

namespace App\Repository;

use App\Entity\Ssp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Ssp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ssp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ssp[]    findAll()
 * @method Ssp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SspRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ssp::class);
    }

    public function findSspForReports()
    {
        return $this->createQueryBuilder('s')
            ->select('s.id id, s.name nicename')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    // /**
    //  * @return Ssp[] Returns an array of Ssp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ssp
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
