<?php

namespace App\Manager;

use App\Repository\DspRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class DspManager extends AbstractManager
{
    /**
     * @var DspRepository
     */
    private $dspRepository;

    /**
     * DspManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameters,
        DspRepository $dspRepository
    ) {
        parent::__construct($container, $parameters);
        $this->dspRepository = $dspRepository;
    }

    public function getDsps()
    {
        return $this->dspRepository->findBy(['deleted' => false]);
    }
}
