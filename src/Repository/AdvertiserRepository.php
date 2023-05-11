<?php

namespace App\Repository;

use App\Entity\Advertiser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Advertiser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advertiser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advertiser[]    findAll()
 * @method Advertiser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertiserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advertiser::class);
    }

    /**
     * @return array
     */
    public function findAdvertisersForReports()
    {
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.name nicename')
                ->getQuery()
                ->getResult(),
            'nicename'
        );
    }

    /**
     * @return mixed
     */
    public function findActiveAdvertisers()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id, a.name, u.username, u.email, u.id userId')
            ->leftJoin('App:User', 'u', 'WITH', 'u.id = a.user')
            ->andWhere('a.deleted = 0')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    // /**
    //  * @return Advertiser[] Returns an array of Advertiser objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advertiser
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
