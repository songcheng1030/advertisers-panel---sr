<?php

namespace App\Controller;

use App\Entity\Agency;
use App\Form\AgencyAjaxType;
use App\Form\AgencyFieldType;
use App\Form\AgencyType;
use App\Form\SearchListType;
use App\Manager\UserManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/agency")
 */
class AgencyController extends VidoomyController
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AgencyController constructor.
     */
    public function __construct(ContainerInterface $container, UserManager $userManager)
    {
        parent::__construct($container);
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="agency_index", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER")
     */
    public function index(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];
            $agencies = $this->userManager->getUserAgencies($loggedUser);

            foreach ($agencies as $agency) {
                $AgencyCountries = $agency->getCountries()->toArray();
                $countries = [];
                foreach ($AgencyCountries as $country) {
                    $countries[] = $country->getName();
                }
                $ret['data'][] = [
                    'id' => $agency->getId(),
                    'name' => $agency->getName(),
                    'type' => $this->translator->trans(AgencyFieldType::$typesNames[$agency->getType()]),
                    'salesManager' => $agency->getSalesManager()->getFullName(),
                    'countries' => $countries,
                    'isAdmin' => $loggedUser->isAdmin(),
                ];
            }

            return new JsonResponse($ret);
        }

        $form = $this->createForm(SearchListType::class);

        return $this->render('agency/index.html.twig', [
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/new-ajax", name="agency_new_ajax", methods={"GET","POST"})
     */
    public function newAjax(Request $request)
    {
        $agency = new Agency();
        $form = $this->createForm(AgencyAjaxType::class, $agency);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $agency->setDeleted(0);
                $this->em->persist($agency);
                $this->em->flush();

                return new Response(
                    json_encode([
                        'status' => 'success',
                        'id' => $agency->getId(),
                        'name' => $agency->getName(),
                    ])
                );
            }

            return new Response(
                json_encode([
                    'status' => 'error',
                    'errors' => $this->getErrorMessages($form, true),
                    'form' => $form->getName(),
                ])
            );
        }

        return $this->render('popup/create_agency.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="agency_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $agency = new Agency();
        $form = $this->createForm(AgencyType::class, $agency);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $agency->setAccountManager($this->userManager->getAccountManagerFormData($form));
                $agency->setDeleted(0);

                $this->em->persist($agency);
                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('agency_index');
            }
            $this->addFlash('error', $this->translator->trans('action_modal.error'));
        }

        return $this->render('agency/new.html.twig', [
            'agency' => $agency,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="agency_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Agency $agency): Response
    {
        $currentRoute = $request->get('_route');
        $form = $this->createForm(AgencyType::class, $agency, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $agency->setAccountManager($this->userManager->getAccountManagerFormData($form));
                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('agency_index');
            }
            $this->addFlash('error', $this->translator->trans('action_modal.error'));
        }

        return $this->render('agency/edit.html.twig', [
            'agency' => $agency,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="agency_show", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER")
     *
     * @return Response
     */
    public function show(Request $request, Agency $agency)
    {
        $currentRoute = $request->get('_route');

        return $this->render('agency/show.html.twig', [
            'agency' => $agency,
            'current_route' => $currentRoute,
            'agencyTypeNames' => AgencyFieldType::$typesNames,
        ]);
    }

    /**
     * @Route("/{id}/archive", name="agency_archive", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function archive(Request $request, Agency $agency): Response
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/archive_agency.html.twig', [
            'agency' => $agency,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="agency_archive_ajax", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function archiveAjax(Request $request, Agency $agency): Response
    {
        if ($this->isCsrfTokenValid('delete' . $agency->getId(), $request->request->get('_token'))) {
            $agency->setDeleted(true);
            $this->em->persist($agency);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('agency_index');
    }

    /**
     * @Route("/{id}/rebate", name="agency_get_rebate", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @return JsonResponse
     */
    public function rebate(Agency $agency)
    {
        return new JsonResponse(['rebate' => $agency->getRebate()]);
    }
}
