<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/old-home", name="app_home")
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $currentRoute = $request->get('_route');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/i18n", name="i18n", methods={"GET"})
     */
    public function i18nAction(Request $request): JsonResponse
    {
        $projectDir = $this->getParameter('kernel.project_dir');

        $user = $this->getUser();
        if (!is_null($user)) {
            $locale = strtoupper($user->getLocale());
            $strings = file_get_contents($projectDir . '/assets/js/datatables/' . $locale . '.json');

            return new JsonResponse($strings, 200, [], true);
        }

        return new JsonResponse([]);
    }
}
