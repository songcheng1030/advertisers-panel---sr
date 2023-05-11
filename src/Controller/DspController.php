<?php

namespace App\Controller;

use App\Entity\Dsp;
use App\Form\DspType;
use App\Repository\DspRepository;
use DateTime;
use DateTimeZone;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dsp")
 */
class DspController extends VidoomyController
{
    /**
     * DspController constructor.
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    /**
     * @Route("/", name="dsp_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(Request $request, DspRepository $dspRepository): Response
    {
        $currentRoute = $request->get('_route');
        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];
            $dsps = $dspRepository->findBy(['deleted' => false]);
            foreach ($dsps as $dsp) {
                $ret['data'][] = [
                    'id' => $dsp->getId(),
                    'name' => $dsp->getName(),
                ];
            }

            return new JsonResponse($ret);
        }

        return $this->render('dsp/index.html.twig', [
                'current_route' => $currentRoute,
            ]);
    }

    /**
     * @Route("/new", name="dsp_new", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     *
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $currentRoute = $request->get('_route');
        $dsp = new Dsp();
        $form = $this->createForm(DspType::class, $dsp, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $dsp->setCreatedAt(new DateTime('now', new DateTimeZone('US/Eastern')));
                $this->em->persist($dsp);
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('dsp_index');
            }
            $this->addFlash('error', $this->translator->trans('action_modal.error'));
        }

        return $this->render('dsp/new.html.twig', [
            'dsp' => $dsp,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/new-ajax", name="dsp_new_ajax", methods={"POST"})
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
            $dsp = new Dsp();
            $dsp->setName($data['name']);
            $dsp->setDeleted(false);
            $dsp->setCreatedAt(new DateTime('now', new DateTimeZone('US/Eastern')));

            $this->em->persist($dsp);
            $this->em->flush();

            return new JsonResponse(['id' => $dsp->getId(), 'name' => $dsp->getName()]);
        }
    }

    /**
     * @Route("/{id}/edit", name="dsp_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Dsp $dsp): Response
    {
        $currentRoute = $request->get('_route');
        $form = $this->createForm(DspType::class, $dsp, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->em->flush();

                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('dsp_index');
            }
            $this->addFlash('error', $this->translator->trans('action_modal.error'));
        }

        return $this->render('dsp/edit.html.twig', [
            'dsp' => $dsp,
            'form' => $form->createView(),
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="dsp_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Dsp $dsp): Response
    {
        $currentRoute = $request->get('_route');

        return $this->render('popup/delete_dsp.html.twig', [
            'dsp' => $dsp,
            'current_route' => $currentRoute,
        ]);
    }

    /**
     * @Route("/{id}", name="dsp_delete_ajax", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteAjax(Request $request, Dsp $dsp): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dsp->getId(), $request->request->get('_token'))) {
            $dsp->setDeleted(true);
            $this->em->persist($dsp);
            $this->em->flush();
            $this->addFlash('success', $this->translator->trans('action_modal.success'));
        }

        return $this->redirectToRoute('dsp_index');
    }
}
