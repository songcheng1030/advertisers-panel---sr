<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchListType;
use App\Form\UserAccountType;
use App\Form\UserType;
use App\Manager\UserManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends VidoomyController
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * UserController constructor.
     */
    public function __construct(ContainerInterface $container, UserManager $userManager, SessionInterface $session)
    {
        parent::__construct($container);
        $this->userManager = $userManager;
        $this->session = $session;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     */
    public function index(Request $request, Packages $assetsManager): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();

        if ($request->isXmlHttpRequest()) {
            $users = $this->userManager->getAdminGroupUsers($loggedUser);

            $ret['data'] = [];
            foreach ($users as $user) {
                $ret['data'][] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'picture' => is_null($user['picture']) ?
                        $assetsManager->getUrl('build/images/default-profile.png') :
                        $user['picture'],
                    'role' => array_map(function ($role) {
                        return $this->translator->trans('users.role.' . strtolower(str_replace('ROLE_', '', $role)));
                    }, $user['roles'])[0],
                    'name' => $user['name'] . ' ' . $user['lastName'],
                    'email' => $user['email'],
                    'isImpersonating' => $this->isGranted('ROLE_PREVIOUS_ADMIN'),
                    'isAdmin' => in_array('ROLE_ADMIN', $user['roles']),
                    'isCampaignViewer' => in_array('ROLE_CAMPAIGN_VIEWER', $user['roles']),
                ];
            }

            return new JsonResponse($ret);
        }

        $form = $this->createForm(SearchListType::class);

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     *
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $validationGroup = $this->userManager->getNewUserValidationGroup($loggedUser, $user->getRoles());

            $violations = $this->validator->validate($user, null, $validationGroup);

            if (0 !== count($violations)) {
                foreach ($violations as $violation) {
                    $form
                        ->get($violation->getPropertyPath())
                        ->addError(
                            new FormError($this->translator->trans($violation->getMessage(), [], 'validators'))
                        );
                }
                $this->addFlash('error', $this->translator->trans('action_modal.error'));
            } else {
                $this->userManager->saveUserManager($form, $loggedUser);

                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('user_index');
            }
        }

        return $this->render('user/new.html.twig', [
            'loggedUser' => $loggedUser,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     *
     * @throws Exception
     */
    public function edit(Request $request, User $user): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $validationGroup = $this->userManager->getEditAccountValidationGroup($loggedUser, $user->getRoles());

            $violations = $this->validator->validate($user, null, $validationGroup);

            if (0 !== count($violations)) {
                foreach ($violations as $violation) {
                    $form
                        ->get($violation->getPropertyPath())
                        ->addError(
                            new FormError($this->translator->trans($violation->getMessage(), [], 'validators'))
                        );
                }
                $this->addFlash('error', $this->translator->trans('action_modal.error'));
            } else {
                $this->userManager->updateUserManager($form, $loggedUser);

                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'loggedUser' => $loggedUser,
        ]);
    }

    /**
     * @Route("/account", name="user_account", methods={"GET", "POST"})
     * @IsGranted("ROLE_SALES_MANAGER")
     *
     * @throws Exception
     */
    public function editAccount(Request $request)
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        $form = $this->createform(
            UserAccountType::class,
            $loggedUser,
            ['validation_groups' => ['account-edition-admin']]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->userManager->updateAccount($form, $loggedUser);
                $locale = $form->get('locale')->getData();
                $this->session->set('_locale', $locale);
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('user_account');
            }
            $this->addFlash('error', $this->translator->trans('action_modal.error'));
        }

        return $this->render('user/account.html.twig', [
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'user' => $loggedUser,
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, User $user): Response
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/delete_user.html.twig', [
            'user' => $user,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete_ajax", methods={"DELETE"})
     * @IsGranted("ROLE_SALES_MANAGER_HEAD")
     */
    public function deleteAjax(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $user->setStatus(User::STATUS_DELETED);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('user_index');
    }
}
