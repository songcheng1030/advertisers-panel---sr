<?php

namespace App\Manager;

use App\Entity\Target;
use App\Entity\User;
use App\Repository\TargetRepository;
use Carbon\Carbon;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TargetManager extends AbstractManager
{
    /**
     * @var TargetRepository
     */
    private $targetRepository;

    /**
     * TargetManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameterBag,
        TargetRepository $targetRepository
    ) {
        parent::__construct($container, $parameterBag);
        $this->targetRepository = $targetRepository;
    }

    /**
     * @return Target
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getTarget(User $user, int $month, int $year)
    {
        $target = $this->targetRepository->findOneBy(['user' => $user->getId(), 'month' => $month, 'year' => $year]);

        if (!$target) {
            $target = new Target();
            $target
                ->setUser($user)
                ->setGoal(0)
                ->setReached(0)
                ->setMonth($month)
                ->setYear($year);

            $this->em->persist($target);
            $this->em->flush();
        }

        return $target;
    }

    /**
     * @return int|mixed|string
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getUserTargets(User $user)
    {
        $this->verifyTargets($user);

        return $this->targetRepository->findByUser($user->getId());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function verifyTargets(User $user)
    {
        $date = Carbon::now()->firstOfMonth();
        for ($i = 1; $i < 12; ++$i) {
            $month = $date->format('n');
            $year = $date->format('Y');
            $target = $this->targetRepository->findOneBy(['user' => $user->getId(), 'month' => $month, 'year' => $year]);
            if (!$target) {
                $target = new Target();
                $target
                    ->setUser($user)
                    ->setGoal(0)
                    ->setReached(0)
                    ->setMonth($month)
                    ->setYear($year);

                $this->em->persist($target);
            }
            $date->addMonth();
        }
        $this->em->flush();
    }
}
