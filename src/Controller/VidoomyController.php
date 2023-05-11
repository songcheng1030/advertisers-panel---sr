<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class VidoomyController extends AbstractController
{
    /**
     * @var object|null
     */
    protected $em;
    /**
     * @var object|null
     */
    protected $translator;
    /**
     * @var object|null
     */
    protected $validator;
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var object|UserPasswordEncoder|null
     */
    protected $password_encoder;

    protected $serializer;

    protected $logger;

    /**
     * VidoomyController constructor.
     */
    public function __construct(ContainerInterface $container, LoggerInterface $logger = null)
    {
        $this->em = $container->get('doctrine.orm.default_entity_manager');
        $this->translator = $container->get('translator');
        $this->validator = $container->get('validator');
        $this->password_encoder = $container->get('security.password_encoder');
        $this->container = $container;
        $this->serializer = $container->get('jms_serializer');

        $this->logger = $logger;
    }

    protected function getErrorMessages(FormInterface $form, bool $isPopup = false)
    {
        $errors = [];
        foreach ($form->getErrors() as $key => $error) {
            $template = $error->getMessageTemplate();
            $parameters = $error->getMessageParameters();

            foreach ($parameters as $var => $value) {
                $template = str_replace($var, $value, $template);
            }

            $errors[$key] = $isPopup ? $this->translator->trans($template) : $template;
        }
        if ($form->count()) {
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrorMessages($child, $isPopup);
                }
            }
        }

        return $errors;
    }

    protected function showErrors(
        ConstraintViolationListInterface $errors,
        FormInterface $form
    ): void {
        foreach ($errors as $error) {
            $form
                ->get($error->getPropertyPath())
                ->addError(
                    new FormError($this->translator->trans($error->getMessage(), [], 'validators'))
                );
        }
    }

    protected function nodeProcOpen(&$pipes)
    {
        $processInputOutputConfig = [
            0 => ['pipe', 'r'],  // stdin is a pipe that the child will read from
            1 => ['pipe', 'w'],  // stdout is a pipe that the child will write to
        ];

        $PROJECT_PATH = getcwd();

        $scriptFilePath = "$PROJECT_PATH/../bin/server-side-renderer.js";

        if (!file_exists($scriptFilePath)) {
            return false;
        }

        $user = $this->getUser();

        $userStr = $this->serializer->serialize($user, 'json');

        $this->logger->critical('aaaaaaaaaaa');
        $this->logger->critical('aaaaaaaaaaa');
        $this->logger->critical('aaaaaaaaaaa');
        $this->logger->critical($userStr);
        $this->logger->critical('aaaaaaaaaaa');
        $this->logger->critical('aaaaaaaaaaa');
        $this->logger->critical('aaaaaaaaaaa');

        return proc_open(
            "node $scriptFilePath '$userStr'",
            $processInputOutputConfig,
            $pipes,
            null, //If null, the process is launched by default where PHP is launched
            null //If null, the process got the same env variables has the one of the parent
        );
    }

    protected function renderWithVue($template, $parameters)
    {
        $content = $this->renderView($template, $parameters);

        $parsedContent = $this->renderVueComponents($content);

        return new Response($parsedContent);
    }

    private function renderVueComponents($originalDom)
    {
        $nodeProcess = $this->nodeProcOpen($pipes);

        $domWithRenderedVueComponents = '';

        if (is_resource($nodeProcess)) {
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /tmp/error-output.txt

            $this->writeIntoPipeAndCloseIt($pipes[0], $originalDom);

            $this->waitForNodeProgramToExit($nodeProcess);

            $domWithRenderedVueComponents = $this->readFromPipeAndClose($pipes[1]);

            proc_close($nodeProcess);

            if ('ERROR: Fail to server-side render' === $domWithRenderedVueComponents
                || false === $domWithRenderedVueComponents
            ) {
                $domWithRenderedVueComponents = $originalDom;
            }
        }

        $whitelinesToBeReplaced = '/\>[\s]*\</'; //This is meant to replace useless extra whitelines

        $content = preg_replace($whitelinesToBeReplaced, '><', $domWithRenderedVueComponents);

        return $content;
    }

    private function readFromPipeAndClose($pipe)
    {
        $content = stream_get_contents($pipe);
        fclose($pipe);

        return $content;
    }

    private function writeIntoPipeAndCloseIt($pipe, $content)
    {
        fwrite($pipe, $content);
        fclose($pipe);
    }

    private function waitForNodeProgramToExit($nodeProcess)
    {
        $status = proc_get_status($nodeProcess);

        while (1 === $status['running']) {
            $status = proc_get_status($nodeProcess);
        }
    }
}
