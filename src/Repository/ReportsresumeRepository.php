<?php

namespace App\Repository;

use App\Entity\Reportsresume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reportsresume|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reportsresume|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reportsresume[]    findAll()
 * @method Reportsresume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReportsresumeRepository extends ServiceEntityRepository
{
    /**
     * ReportsresumeRepository constructor.
     *
     * @param PublisherRepository $publisherRepository
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reportsresume::class);
    }

    public function generateNewReport($uid, $user_id)
    {
        $tablename = 'report_key';

        $conn = $this->getEntityManager()->getConnection();

        $sql = "INSERT INTO $tablename (unique_id, user_id, type, status) VALUES ('$uid', '$user_id', 1, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return true;
    }
}
