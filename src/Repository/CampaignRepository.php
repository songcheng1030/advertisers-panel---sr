<?php

namespace App\Repository;

use App\Entity\Campaign;
use App\Form\CampaignStatusFieldType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Campaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method Campaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method Campaign[]    findAll()
 * @method Campaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }

    /**
     * @return mixed
     */
    public function findForAdmin()
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.name, c.dealId, a.name agency, c.status, c.status')
            ->from('App:Agency', 'a')
            ->from('App:Ssp', 's')
            ->where('c.agency = a.id')
            ->andWhere('c.ssp = s.id')
            ->andWhere('c.status != :status_id')
            ->setParameters([
                'status_id' => CampaignStatusFieldType::STATUS_ARCHIVED,
            ])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @return mixed
     */
    public function findForSalesManager(int $userId)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.name, c.dealId, a.name agency, c.status, c.status')
            ->from('App:Agency', 'a')
            ->from('App:Ssp', 's')
            ->where('c.agency = a.id')
            ->andWhere('c.ssp = s.id')
            ->andWhere('c.status != :status_id')
            ->andWhere('a.salesManager = :user_id')
            ->setParameters([
                'user_id' => $userId,
                'status_id' => CampaignStatusFieldType::STATUS_ARCHIVED,
            ])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    public function findForSalesManagerHead(int $userId)
    {
        return $this->createQueryBuilder('c')
            ->select('c.id, c.name, c.dealId, a.name agency, c.status, c.status')
            ->from('App:Agency', 'a')
            ->from('App:Ssp', 's')
            ->from('App:User', 'u')
            ->where('c.agency = a.id')
            ->andWhere('a.salesManager = u.id')
            ->andWhere('c.ssp = s.id')
            ->andWhere('c.status != :status_id')
            ->andwhere('a.salesManager = :user_id OR u.salesManagerHead = :user_id')
            ->setParameters([
                'user_id' => $userId,
                'status_id' => CampaignStatusFieldType::STATUS_ARCHIVED,
            ])
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);
    }

    public function findCampaignsForReportsSalesManagerHead(int $userId, $requestedField)
    {
        if (!$requestedField || empty($requestedField)) {
            $requestedField = 'name';
        }

        return array_column(
            $this->createQueryBuilder('c')
                ->select('COALESCE(c.' . $requestedField . ', \'\') nicename')
                ->from('App:User', 'u')
                ->andWhere('c.advertiser = u.id')
                ->andwhere('u.id = :user_id OR u.salesManagerHead = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'nicename'
        );
    }

    public function findCampaignsForReportsSalesManager(int $userId, $requestedField)
    {
        if (!$requestedField || empty($requestedField)) {
            $requestedField = 'name';
        }

        return array_column(
            $this->createQueryBuilder('c')
                ->select('COALESCE(c.' . $requestedField . ', \'\') nicename')
                ->from('App:User', 'u')
                ->where('c.advertiser = u')
                ->andWhere('u.id = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'nicename'
        );
    }

    public function findCampaignsForReportsAdvertisers(int $userId, $requestedField)
    {
        if (!$requestedField || empty($requestedField)) {
            $requestedField = 'name';
        }

        return array_column(
            $this->createQueryBuilder('c')
                ->select('COALESCE(c.' . $requestedField . ', \'\') nicename')
                ->from('App:Advertiser', 'a')
                ->from('App:User', 'u')
                ->where('c.advertiser = a')
                ->andWhere('a.user = u')
                ->andWhere('u.id = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'nicename'
        );
    }

    public function findCampaignsForReportsAdmin(int $userId, $requestedField)
    {
        if (!$requestedField || empty($requestedField)) {
            $requestedField = 'name';
        }

        return array_column(
            $this->createQueryBuilder('c')
                ->select('c.id id, COALESCE(c.' . $requestedField . ', \'\') nicename')
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'nicename'
        );
    }

    public function findCampaignsForReportsCampaignViewer(int $userId, $requestedField)
    {
        if (!$requestedField || empty($requestedField)) {
            $requestedField = 'name';
        }

        return array_column(
            $this->createQueryBuilder('c')
                ->select('COALESCE(c.' . $requestedField . ', \'\') nicename')
                ->leftJoin('c.users', 'u')
                ->where('u.id = :user_id')
                ->setParameters([
                    'user_id' => $userId,
                ])
                ->getQuery()
                ->getResult(Query::HYDRATE_ARRAY),
            'nicename'
        );
    }

    // /**
    //  * @return Campaign[] Returns an array of Campaign objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Campaign
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
