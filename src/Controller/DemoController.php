<?php

namespace App\Controller;

use App\Entity\Demo;
use App\Form\DemoType;
use App\Form\SearchListType;
use App\Manager\DemoManager;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/demo")
 */
class DemoController extends VidoomyController
{
    /**
     * CampaignController constructor.
     */
    public function __construct(
        ContainerInterface $container,
        DemoManager $demoManager
    ) {
        parent::__construct($container);
        $this->demoManager = $demoManager;
    }

    /**
     * @Route("/", name="demo_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DEMO')")
     */
    public function index(Request $request, Packages $assetsManager, TranslatorInterface $translator): Response
    {
        $currentRoute = $request->get('_route');
        if ($request->isXmlHttpRequest()) {
            $ret['data'] = [];
            $demos = $this->em->getRepository(Demo::class)->findAll();

            foreach ($demos as $demo) {
                $aux = explode('/', explode('?', $demo->getVideo())[0]);
                $ret['data'][] = [
                    'id' => $demo->getId(),
                    'url' => '<a id="url_' . $demo->getId() . '" class="url" href="https://vidoomy.com/demos/' . (('demo' != @$demo->getUrlFormat()) ? $demo->getUrlFormat() . '/' : '') . $demo->getUrl() . '" target="_blank">' . '/' . $demo->getUrl() . '</a>',
                    'format' => ucfirst($demo->getFormat()),
                    'status' => $this->translator->trans('demo.status.' . (@$demo->getStatus() ? $demo->getStatus() : 0)),
                    'video' => (@$demo->getVideo()) ? '<a class="url" href="' . $demo->getVideo() . '" target="_blank">' . end($aux) . '</a>' : '',
                    'date' => $demo->getDate()->format('m-d-yy'),
                ];
            }

            return new JsonResponse($ret);
        }

        $form = $this->createForm(SearchListType::class);

        return $this->render('demo/index.html.twig', [
            'controller_name' => 'DemoController',
            'current_route' => $currentRoute,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="demo_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DEMO')")
     */
    public function new(Request $request)
    {
        $isEdit = false;
        $currentRoute = $request->get('_route');
        $demo = new Demo();
        $demo->setDate(new DateTime('now'));
        $demo->setOrientation('landscape');
        $form = $this->createForm(DemoType::class, $demo, ['is_edit' => $isEdit]);
        $form->handleRequest($request);

        return $this->loadOrSave($form, $demo, $currentRoute, $isEdit);
    }

    /**
     * @Route("/{id}/edit", name="demo_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_DEMO')")
     */
    public function editDemo(Request $request, $id)
    {
        $isEdit = true;
        $demo = $this->em->getRepository(Demo::class)->find($id);
        $currentRoute = $request->get('_route');
        // $demo->setDate(new DateTime('now'));
        if (null == $demo->getOrientation()) {
            $demo->setOrientation('landscape');
        }
        $form = $this->createForm(DemoType::class, $demo, ['is_edit' => $isEdit]);
        $form->handleRequest($request);

        return $this->loadOrSave($form, $demo, $currentRoute, $isEdit);
    }

    public function loadOrSave(FormInterface $form, Demo $demo, $currentRoute, $isEdit)
    {
        if ($form->isSubmitted()) {
            $errors = $this->demoManager->getNewDemoValidation($form, $demo);

            if ($errors) {
                $this->addFlash('error', $this->translator->trans('action_modal.error'));
            } else {
                $w = '';
                $h = '';
                $w_m = '';
                $h_m = '';
                switch ($demo->getFormat()) {
                    case 'intext':
                        $w = '640';
                        $h = '360';

                        break;
                    case 'slider':
                        $w = '400';
                        $h = '225';

                        break;
                }
                if ('landscape' == $demo->getOrientation()) {
                    $w_m = '274';
                    $h_m = '154';
                } elseif ('portrait' == $demo->getOrientation()) {
                    $w_m = '230';
                    $h_m = '409';
                }

                if ($demo->getDesktop()) {
                    if (!@$demo->getWidth()) {
                        $demo->setWidth($w);
                    }

                    if (!@$demo->getHeight()) {
                        $demo->setHeight($h);
                    }
                } else {
                    $demo->setWidth(null);
                    $demo->setHeight(null);
                }

                if ($demo->getMobile()) {
                    if (!@$demo->getWidthMobile()) {
                        $demo->setWidthMobile($w_m);
                    }
                    if (!@$demo->getHeightMobile()) {
                        $demo->setHeightMobile($h_m);
                    }
                } else {
                    $demo->setWidthMobile(null);
                    $demo->setHeightMobile(null);
                }

                $video = $form->get('video')->getData();

                if ($video && false !== strpos($video, 'base64')) {
                    $demo = $this->demoManager->uploadVideo($video, $demo);
                    if (null != $demo->getId()) {
                        $demo->setStatus(3);
                    }
                } else {
                    if (null != $demo->getId()) {
                        $demo_original = $this->em->createQueryBuilder('aux')
                            ->select('d.click_url')
                            ->from('App:Demo', 'd')
                            ->where('d.id = :id')
                            ->setParameters([
                                'id' => $demo->getId(),
                            ])
                            ->getQuery()
                            ->getResult();
                        if ($demo->getClickUrl() != $demo_original[0]['click_url']) {
                            $demo->setStatus(5);
                        }
                    }
                }

                $this->em->persist($demo);
                $this->em->flush();
                $this->addFlash('success', $this->translator->trans('action_modal.success'));

                return $this->redirectToRoute('demo_index');
            }
        }

        return $this->render('demo/new_or_edit.html.twig', [
            'controller_name' => 'DemoController',
            'current_route' => $currentRoute,
            'form' => $form->createView(),
            'demo' => $demo,
            'isEdit' => $isEdit,
        ]);
    }
}
