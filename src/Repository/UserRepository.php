<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getAdminGroupUsers(): array
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('
                    u.id,
                    u.username,
                    u.name,
                    u.lastName,
                    u.email,
                    u.roles,
                    u.picture')
            ->where('u.status = :status_id')
            ->andWhere("u.roles LIKE '%" . implode("%' OR u.roles LIKE '%", User::$adminGroupUsers) . "%'")
            ->setParameter('status_id', User::STATUS_ACTIVE);

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findSalesManagersForReportsSalesManagerHead($userId)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('
                    u.id id, CONCAT (u.name, \' \', u.lastName) nicename')
            ->where('u.salesManagerHead = ' . $userId)
            ->andWhere('JSON_CONTAINS(u.roles, \'"ROLE_ADVERTISER"\', \'$\') = 0');

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findSalesManagersForReportsAdmin($userId)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('
                    u.id id, CONCAT (u.name, \' \', u.lastName) nicename')
            ->where('JSON_CONTAINS(u.roles, \'"ROLE_ADVERTISER"\', \'$\') = 0');

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function getSalesManagersForSalesManagerHead(?UserInterface $loggerUser)
    {
        $queryBuilder = $this->createQueryBuilder('u')
            ->select('
                    u.id,
                    u.username,
                    u.name,
                    u.lastName,
                    u.email,
                    u.roles,
                    u.picture')
            ->where('u.status = :status_id')
            ->andWhere('u.salesManagerHead = ' . $loggerUser->getId())
            ->setParameter('status_id', User::STATUS_ACTIVE);

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param $roles
     *
     * @return mixed
     */
    public function findByRoles(array $roles)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where("u.roles LIKE '%$roles[0]%'");

        if (count($roles) > 1) {
            for ($i = 1; $i < count($roles); ++$i) {
                $qb->orWhere("u.roles LIKE '%$roles[$i]%'");
            }
        }

        $qb->andWhere('u.status = :status_id')
            ->setParameters([
            'status_id' => User::STATUS_ACTIVE,
        ]);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
