<?php

namespace App\Controller;

use App\Entity\Advertiser;
use App\Entity\User;
use App\Form\AdvertiserType;
use App\Manager\AdvertiserManager;
use App\Manager\UserManager;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @Route("/advertiser")
 */
class AdvertiserController extends VidoomyController
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var AdvertiserManager
     */
    private $advertiserManager;

    /**
     * AdvertiserController constructor.
     */
    public function __construct(
        ContainerInterface $container,
        UserManager $userManager,
        AdvertiserManager $advertiserManager
    ) {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->advertiserManager = $advertiserManager;
    }

    /**
     * @Route("/", name="advertiser_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];
            $advertisers = $this->advertiserManager->getAdvertiserUsers();
            foreach ($advertisers as $advertiser) {
                $ret['data'][] = [
                    'id' => $advertiser['id'],
                    'name' => $advertiser['name'],
                    'username' => $advertiser['username'],
                    'email' => $advertiser['email'],
                    'isImpersonating' => $this->isGranted('ROLE_PREVIOUS_ADMIN'),
                    'hasUser' => null !== $advertiser['userId'],
                ];
            }

            return new JsonResponse($ret);
        }

        return $this->render('advertiser/index.html.twig', [
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/new", name="advertiser_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        $advertiser = new Advertiser();
        $user = new User();
        $form = $this->createForm(AdvertiserType::class, null, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $createdAt = new DateTime('now', new DateTimeZone('US/Eastern'));
            $createUser = $form->get('createUser')->getData();

            $advertiser->setName($form->get('name')->getData());
            $advertiser->setCreatedAt($createdAt);

            if (!$createUser) {
                if ($this->processAdvertiser($advertiser, $form)) {
                    return $this->redirectToRoute('advertiser_index');
                }
            } else {
                $this->userManager->setUserData($user, $form, $loggedUser, $createdAt);

                $advertiserErrors = $this->validator->validate($advertiser, null, 'advertiser-user');
                $userErrors = $this->validator->validate($user, null, 'advertiser-user');

                if ($this->processAdvertiserUser($advertiserErrors, $userErrors, $form, $advertiser, $user)) {
                    return $this->redirectToRoute('advertiser_index');
                }
            }
        }

        return $this->render('advertiser/new.html.twig', [
            'advertiser' => $advertiser,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'has_user' => false,
        ]);
    }

    /**
     * @Route("/new-ajax", name="advertiser_new_ajax", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function newAjax(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (array_key_exists('name', $data)) {
            $advertiser = new Advertiser();
            $advertiser->setName($data['name']);
            $advertiser->setDeleted(false);
            $advertiser->setCreatedAt(new DateTime('now', new DateTimeZone('US/Eastern')));

            $this->em->persist($advertiser);
            $this->em->flush();

            return new JsonResponse(['id' => $advertiser->getId(), 'name' => $advertiser->getName()]);
        }

        return new JsonResponse(['error' => $this->translator->trans('advertisers.name_must_be_provided')]);
    }

    /**
     * @Route("/{id}/edit", name="advertiser_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws ORMException
     * @throws Exception
     */
    public function edit(Request $request, Advertiser $advertiser): Response
    {
        $currentRoute = $request->get('_route');
        $user = $advertiser->getUser();
        $loggedUser = $this->getUser();
        $hasUser = false;
        if ($user) {
            $hasUser = true;
        } else {
            $user = new User();
        }
        $form = $this->createForm(AdvertiserType::class, $advertiser, ['is_edit' => true, 'has_user' => $hasUser]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $createdAt = new DateTime('now', new DateTimeZone('US/Eastern'));
            $createUser = true;

            if (!$hasUser) {
                $createUser = $form->get('createUser')->getData();
            }

            if (!$createUser) {
                if ($this->processAdvertiser($advertiser, $form)) {
                    return $this->redirectToRoute('advertiser_index');
                }
            } else {
                $this->userManager->setUserData($user, $form, $loggedUser, $createdAt);
                $userGroupValidation = $this->userManager->getUserGroupValidation($hasUser, $request);

                $advertiserErrors = $this->validator->validate($advertiser, null, 'advertiser-user');
                $userErrors = $this->validator->validate($user, null, $userGroupValidation);

                if ($this->processAdvertiserUser($advertiserErrors, $userErrors, $form, $advertiser, $user)) {
                    return $this->redirectToRoute('advertiser_index');
                }
            }
        }

        return $this->render('advertiser/edit.html.twig', [
            'advertiser' => $advertiser,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'has_user' => $hasUser,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="advertiser_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Advertiser $advertiser): Response
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/delete_advertiser.html.twig', [
            'advertiser' => $advertiser,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="advertiser_delete_ajax", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws ORMException
     */
    public function deleteAjax(Request $request, Advertiser $advertiser): Response
    {
        if ($this->isCsrfTokenValid('delete' . $advertiser->getId(), $request->request->get('_token'))) {
            $advertiser->setDeleted(true);
            $this->em->persist($advertiser);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('advertiser_index');
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function processAdvertiser(Advertiser $advertiser, FormInterface $form): bool
    {
        $errors = $this->validator->validate($advertiser, null, 'advertiser');

        if (0 !== count($errors)) {
            $this->showErrors($errors, $form);
            $this->addFlash('error', $this->translator->trans('action_modal.error'));

            return false;
        }

        $this->em->persist($advertiser);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('action_modal.success'));

        return true;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    protected function processAdvertiserUser(
        ConstraintViolationListInterface $advertiserErrors,
        ConstraintViolationListInterface $userErrors,
        FormInterface $form,
        Advertiser $advertiser,
        User $user
    ): bool {
        if (0 !== count($advertiserErrors) || 0 !== count($userErrors)) {
            $this->showErrors($advertiserErrors, $form);
            $this->showErrors($userErrors, $form);

            $this->addFlash('error', $this->translator->trans('action_modal.error'));

            return false;
        }

        $advertiser->setUser($user);
        $this->em->persist($advertiser);
        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', $this->translator->trans('action_modal.success'));

        return true;
    }
}
