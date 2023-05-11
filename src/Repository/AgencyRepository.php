<?php

namespace App\Repository;

use App\Entity\Agency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Agency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Agency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Agency[]    findAll()
 * @method Agency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AgencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agency::class);
    }

    public function findAgenciesForSalesManagerHead(int $userId)
    {
        return $this->createQueryBuilder('a')
            ->from('App:User', 'u')
            ->where('a.salesManager = u.id')
            ->andWhere('a.deleted = 0')
            ->andwhere('a.salesManager = :user_id OR u.salesManagerHead = :user_id')
            ->setParameters([
                'user_id' => $userId,
            ])
            ->getQuery()
            ->getResult();
    }

    public function findAgenciesForReportsSalesManagerHead(int $userId)
    {
        // sales manager head ve aquellas q salesmanagerhead sea él, o aquellas cuyo sales manager normal tenga como head él
        // sales manager normal ve la(s) suyas
        // admin lo ve todo
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.name as nicename')
                ->from('App:User', 'u')
                ->where('a.salesManager = u.id')
                ->andWhere('a.deleted = 0')
                ->andwhere('a.salesManager = :user_id OR u.salesManagerHead = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(),
            'nicename'
        );
    }

    public function findAgenciesForReportsSalesManager(int $userId)
    {
        // sales manager head ve aquellas q salesmanagerhead sea él, o aquellas cuyo sales manager normal tenga como head él
        // sales manager normal ve la(s) suyas
        // admin lo ve todo
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.name as nicename')
                ->from('App:User', 'u')
                ->where('a.salesManager = u.id')
                ->andWhere('a.deleted = 0')
                ->andwhere('a.salesManager = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(),
            'nicename'
        );
    }

    public function findAgenciesForReportsAdmin(int $userId)
    {
        // sales manager head ve aquellas q salesmanagerhead sea él, o aquellas cuyo sales manager normal tenga como head él
        // sales manager normal ve la(s) suyas
        // admin lo ve todo
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.name as nicename')
                ->getQuery()
                ->getResult(),
            'nicename'
        );
    }

    // /**
    //  * @return Agency[] Returns an array of Agency objects
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
    public function findOneBySomeField($value): ?Agency
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
