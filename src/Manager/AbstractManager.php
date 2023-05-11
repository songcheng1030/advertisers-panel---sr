<?php

namespace App\Manager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AbstractManager
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
     * @var ParameterBagInterface
     */
    protected $parameters;
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var object|null
     */
    protected $password_encoder;

    /**
     * AbstractManager constructor.
     */
    public function __construct(ContainerInterface $container, ParameterBagInterface $parameters)
    {
        $this->em = $container->get('doctrine.orm.default_entity_manager');
        $this->translator = $container->get('translator');
        $this->password_encoder = $container->get('security.password_encoder');
        $this->parameters = $parameters;
        $this->container = $container;
    }
}
