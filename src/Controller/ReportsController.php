<?php

namespace App\Controller;

use App\Entity\Advertiser;
use App\Entity\Country;
use App\Entity\Dsp;
use App\Entity\Reportsresume;
use App\Entity\Ssp;
use App\Manager\UserManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends VidoomyController
{
    private $userManager;

    /**
     * ReportsController constructor.
     */
    public function __construct(ContainerInterface $container, UserManager $userManager, LoggerInterface $logger)
    {
        parent::__construct($container, $logger);
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="reports")
     */
    public function index(Request $request)
    {
        $loggedUser = $this->getUser();

        $currentRoute = $request->get('_route');

        return $this->renderWithVue('reports/index.html.twig', [
            'controller_name' => 'ReportsController',
            'current_route' => $currentRoute,
            'cards_info' => $loggedUser->getReportsCards(),
        ]);
    }

    /**
     * @Route("/reports/genrepo/{uid}", name="endpoint_generate_report", methods={"GET"})
     */
    public function newReport(Request $request)
    {
        $user = $this->getUser();

        $uid = $request->get('uid');
        $reports = $this
            ->em
            ->getRepository(Reportsresume::class);

        $reports->generateNewReport($uid, $user->getId());

        return new JsonResponse(['OK']);
    }

    /**
     * @Route("/predictive_content/{dim}", name="reports_predictives")
     */
    public function predictives(Request $request, $dim)
    {
        $loggedUser = $this->getUser();
        $predictiveArray = [];

        switch ($dim) {
            case 'type':
                $predictiveArray[] = 'Campaign';
                $predictiveArray[] = 'Deal';

            break;
            case 'sales_manager':
                $predictiveArray = $this->userManager->getReportsUserSalesManager($loggedUser);

            break;
            case 'campaign_name':
                $predictiveArray = $this->userManager->getReportsUserCampaigns($loggedUser, 'name');

            break;
            case 'deal_id':
                // de la tabla de campañas, columna deal id en vez del name
                $predictiveArray = $this->userManager->getReportsUserCampaigns($loggedUser, 'dealId');

            break;
            case 'advertiser':
                $advertiserRepo = $this
                    ->em
                    ->getRepository(Advertiser::class);
                $predictiveArray = $advertiserRepo->findAdvertisersForReports($loggedUser);

            break;
            case 'agency':
                $predictiveArray = $this->userManager->getReportsUserAgencies($loggedUser);

            break;
            case 'country':
                $countryRepo = $this
                    ->em
                    ->getRepository(Country::class);
                $predictiveArray = $countryRepo->findCountriesForReports();

            break;
            case 'ssp':
                $sspRepo = $this
                    ->em
                    ->getRepository(Ssp::class);
                $predictiveArray = $sspRepo->findSspForReports();

            break;
            case 'dsp':
                $dspRepo = $this
                    ->em
                    ->getRepository(Dsp::class);
                $predictiveArray = $dspRepo->findDspForReports();

            break;
        }

        return new JsonResponse($predictiveArray);
    }
}
