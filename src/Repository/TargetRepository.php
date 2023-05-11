<?php

namespace App\Repository;

use App\Entity\Target;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Target|null find($id, $lockMode = null, $lockVersion = null)
 * @method Target|null findOneBy(array $criteria, array $orderBy = null)
 * @method Target[]    findAll()
 * @method Target[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TargetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Target::class);
    }

    public function findByUser(int $userId)
    {
        return $this->createQueryBuilder('t')
            ->select('t.id, t.goal, t.reached, t.month, t.year')
            ->where('t.user = :user_id')
            ->orderBy('t.year', 'DESC')
            ->addOrderBy('t.month', 'DESC')
            ->setParameters([
                'user_id' => $userId,
            ])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    // /**
    //  * @return Target[] Returns an array of Target objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Target
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
