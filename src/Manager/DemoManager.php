<?php

namespace App\Manager;

use App\Entity\Demo;
use Gaufrette\Extras\Resolvable\ResolvableFilesystem;
use Gaufrette\Extras\Resolvable\Resolver\AwsS3PublicUrlResolver;
use Gaufrette\Extras\Resolvable\Resolver\StaticUrlResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

class DemoManager extends AbstractManager
{
    /**
     * DemoManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameters
    ) {
        parent::__construct($container, $parameters);
    }

    /**
     * @return bool
     */
    public function getNewDemoValidation(FormInterface $form, Demo $demo)
    {
        $hasError = false;
        if (null == $demo->getUrl() || 0 == strlen('' . trim($demo->getUrl()))) {
            $hasError = true;
            $form
                ->get('url')
                ->addError(
                    new FormError($this->translator->trans(
                        'common.demo_url_is_null',
                        [],
                        'validators'
                    ))
                );
        }
        if (!$demo->getDesktop() && !$demo->getMobile()) {
            $form
                ->get('desktop')
                ->addError(
                    new FormError($this->translator->trans(
                        'common.demo_validation_desk_mob',
                        [],
                        'validators'
                    ))
                );
            $form
                ->get('mobile')
                ->addError(
                    new FormError($this->translator->trans(
                        'common.demo_validation_desk_mob',
                        [],
                        'validators'
                    ))
                );
            $hasError = true;
        }

        if ($demo->getMobile()) {
            if (null == $demo->getOrientation()) {
                $form
                ->get('orientation')
                ->addError(
                    new FormError($this->translator->trans(
                        'common.demo_url_is_null',
                        [],
                        'validators'
                    ))
                );
                $hasError = true;
            }
        }

        if (@$demo->getClickUrl() && !preg_match('/^((http)s?(:\/\/))([a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+)+((\/[.a-zA-Z0-9_-]+)*(\/)?)?(\?[a-zA-Z0-9_-]+(\=[,a-zA-Z0-9\/.%_-]+)?(\&[,a-zA-Z0-9_-]+(\=[,a-zA-Z0-9\/.%_-]+)?)*)?$/', $demo->getClickUrl())) {
            $form
            ->get('click_url')
                ->addError(
                    new FormError($this->translator->trans(
                        'common.demo_validation_click_url_invalid',
                        [],
                        'validators'
                    ))
                );
            $hasError = true;
        }

        return $hasError;
    }

    /**
     * @throws UnresolvableObjectException
     */
    public function uploadVideo(string $data, Demo $demo, bool $alreadyParsed = false): Demo
    {
        if (!$alreadyParsed) {
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            list(, $type) = explode('/', $type);
        }

        $videoData = base64_decode($data);
        //$videoInfo = getimagesizefromstring($videoData);
        $extension = $type;
        $videoFilename = $this->randomName() . '.' . $extension;

        $filesystem = $this->getFilesystem();

        if ($filesystem->has($videoFilename)) {
            $filesystem->delete($videoFilename);
        }
        $filesystem->write($videoFilename, $videoData);
        $fileUrl = $filesystem->resolve($videoFilename);

        $demo->setVideo($fileUrl);

        return $demo;
    }

    private function getFilesystem(): ResolvableFilesystem
    {
        if ('devv' === $this->container->get('kernel')->getEnvironment()) {
            $resolver = new StaticUrlResolver(
                'http://localhost:8000/uploads/video/'
            );
        } else {
            $resolver = new AwsS3PublicUrlResolver(
                $this->container->get('ct_file_store.s3'),
                $this->parameters->get('uploads_s3_bucket'),
                '/video'
            );
        }

        $nativeFS = $this->container->get('knp_gaufrette.filesystem_map')->get('videos_fs');

        return new ResolvableFilesystem(
            $nativeFS,
            $resolver
        );
    }

    private function randomName($length = 8)
    {
        return substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}
