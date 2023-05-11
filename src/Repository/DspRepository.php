<?php

namespace App\Repository;

use App\Entity\Dsp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Dsp|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dsp|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dsp[]    findAll()
 * @method Dsp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DspRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dsp::class);
    }

    public function findDspForReports()
    {
        return $this->createQueryBuilder('d')
            ->select('d.id id, d.name nicename')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    // /**
    //  * @return Dsp[] Returns an array of Dsp objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Dsp
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
