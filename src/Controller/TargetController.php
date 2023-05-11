<?php

namespace App\Controller;

use App\Entity\Target;
use App\Entity\User;
use App\Manager\TargetManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/target")
 */
class TargetController extends VidoomyController
{
    /**
     * @var TargetManager
     */
    private $targetManager;

    /**
     * TargetController constructor.
     */
    public function __construct(ContainerInterface $container, TargetManager $targetManager)
    {
        parent::__construct($container);
        $this->targetManager = $targetManager;
    }

    /**
     * @Route("/{id}/", name="target_index", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     *
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function index(Request $request, User $user): Response
    {
        $currentRoute = $request->get('_route');

        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];

            $targets = $this->targetManager->getUserTargets($user);

            foreach ($targets as $target) {
                $ret['data'][] = [
                    'id' => $target['id'],
                    'goal' => $target['goal'],
                    'reached' => $target['reached'],
                    'month' => $target['month'],
                    'year' => $target['year'],
                ];
            }

            return new JsonResponse($ret);
        }

        return $this->render('target/index.html.twig', [
            'user_id' => $user->getId(),
            'user_full_name' => $user->getFullName(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/update", name="target_update", methods={"POST"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     *
     * @throws ORMException
     */
    public function update(Request $request, Target $target)
    {
        $data = $request->request->all();
        $target->fromArray($data);
        $this->em->persist($target);
        $this->em->flush();

        return new JsonResponse(['Ok']);
    }
}
