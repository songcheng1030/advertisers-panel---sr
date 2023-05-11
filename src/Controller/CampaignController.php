<?php

namespace App\Controller;

use App\Entity\Campaign;
use App\Form\CampaignStatusFieldType;
use App\Form\CampaignType;
use App\Form\SearchListType;
use App\Manager\CampaignManager;
use App\Manager\UserManager;
use App\Repository\CampaignRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/campaign")
 */
class CampaignController extends VidoomyController
{
    /**
     * @var CampaignManager
     */
    private $campaignManager;
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * CampaignController constructor.
     */
    public function __construct(
        ContainerInterface $container,
        CampaignManager $campaignManager,
        UserManager $userManager
    ) {
        parent::__construct($container);
        $this->campaignManager = $campaignManager;
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="campaign_index", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER")
     */
    public function index(Request $request, CampaignRepository $campaignRepository): Response
    {
        $currentRoute = $request->get('_route');
        $loggedUser = $this->getUser();
        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];
            $campaigns = $this->userManager->getUserCampaigns($loggedUser);

            foreach ($campaigns as $campaign) {
                $ret['data'][] = [
                    'id' => $campaign['id'],
                    'name' => $campaign['name'],
                    'deal_id' => $campaign['dealId'],
                    'agency' => $campaign['agency'],
                    'status' => $this->translator->trans(CampaignStatusFieldType::$statusNames[$campaign['status']]),
                    'isAdmin' => $loggedUser->isAdmin(),
                    'isActive' => CampaignStatusFieldType::STATUS_ACTIVE === $campaign['status'],
                ];
            }

            return new JsonResponse($ret);
        }

        $form = $this->createForm(SearchListType::class);

        return $this->render('campaign/index.html.twig', [
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/new", name="campaign_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $campaign = new Campaign();
        $form = $this->createForm(CampaignType::class, $campaign);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $validationGroup = $this->campaignManager->getCampaignNewValidationGroup($request, $campaign);

            $hasListError = $this->campaignManager->setListFiles($form, $campaign);
            $hasDealIdError = $this->campaignManager->checkDealId($form, $campaign);
            $hasDateErrors = $this->campaignManager->checkDates($form, $campaign);
            $errors = $this->validator->validate($campaign, null, $validationGroup);

            if (0 !== count($errors) || $hasListError || $hasDealIdError || $hasDateErrors) {
                foreach ($errors as $error) {
                    $form->get($error->getPropertyPath())->addError(new FormError($error->getMessage()));
                }
                $this->addFlash('error', $this->translator->trans('action_modal.error'));
            } else {
                $campaign->setDeleted(0);
                $campaign->setCreatedAt(new DateTime('now', new DateTimeZone('US/Eastern')));
                $this->em->persist($campaign);
                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('campaign_index');
            }
        }

        return $this->render('campaign/new.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'is_edit' => false,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="campaign_edit", methods={"GET","POST"})
     * @Route("/{id}/clone", name="campaign_clone", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws Exception
     */
    public function edit(Request $request, Campaign $selectedCampaign): Response
    {
        $currentRoute = $request->get('_route');
        $campaign = $this->campaignManager->getCampaign($request, $selectedCampaign);
        $form = $this->createForm(CampaignType::class, $campaign,
            ['is_edit' => true, 'is_clone' => 'campaign_clone' === $currentRoute]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hasListError = $this->campaignManager->setListFiles($form, $campaign);
            $hasDateErrors = $this->campaignManager->checkDates($form, $campaign);

            $validationGroup = $this->campaignManager->getCampaignEditValidationGroup($request);
            $errors = $this->validator->validate($campaign, null, $validationGroup);

            if (0 !== count($errors) || $hasListError || $hasDateErrors) {
                foreach ($errors as $error) {
                    $form->get($error->getPropertyPath())->addError(new FormError($error->getMessage()));
                }
                $this->addFlash('error', $this->translator->trans('action_modal.error'));
            } else {
                $this->campaignManager->setCostType($campaign, $request);
                $this->em->persist($campaign);
                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('campaign_index');
            }
        }

        return $this->render('campaign/edit.html.twig', [
            'campaign' => $campaign,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
            'is_edit' => true,
        ]);
    }

    /**
     * @Route("/{id}", name="campaign_show", methods={"GET"})
     * @IsGranted("ROLE_SALES_MANAGER")
     *
     * @return Response
     */
    public function show(Request $request, Campaign $campaign)
    {
        $currentRoute = $request->get('_route');

        return $this->render('campaign/show.html.twig', [
            'campaign' => $campaign,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/archive", name="campaign_archive", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function archive(Request $request, Campaign $campaign): Response
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/archive_campaign.html.twig', [
            'campaign' => $campaign,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="campaign_delete", methods={"DELETE"})
     *
     * @throws ORMException
     */
    public function delete(Request $request, Campaign $campaign): Response
    {
        if ($this->isCsrfTokenValid('delete' . $campaign->getId(), $request->request->get('_token'))) {
            $campaign->setStatus(CampaignStatusFieldType::STATUS_ARCHIVED);
            $this->em->persist($campaign);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('campaign_index');
    }

    /**
     * @Route("/{id}/activate-confirmation", name="campaign_activate_confirmation", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function activateConfirmation(Request $request, Campaign $campaign)
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/activate_campaign.html.twig', [
            'campaign' => $campaign,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/activate", name="campaign_activate", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @return RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function activate(Request $request, Campaign $campaign)
    {
        if ($this->isCsrfTokenValid('activate' . $campaign->getId(), $request->request->get('_token'))) {
            $campaign->setStatus(CampaignStatusFieldType::STATUS_ACTIVE);
            $this->em->persist($campaign);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('campaign_index');
    }

    /**
     * @Route("/{id}/pause-confirmation", name="campaign_pause_confirmation", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function pauseConfirmation(Request $request, Campaign $campaign)
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/pause_campaign.html.twig', [
            'campaign' => $campaign,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}/pause", name="campaign_pause", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @return RedirectResponse
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function pause(Request $request, Campaign $campaign)
    {
        if ($this->isCsrfTokenValid('pause' . $campaign->getId(), $request->request->get('_token'))) {
            $campaign->setStatus(CampaignStatusFieldType::STATUS_PAUSED);
            $this->em->persist($campaign);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('campaign_index');
    }
}
