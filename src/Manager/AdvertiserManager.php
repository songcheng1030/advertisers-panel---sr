<?php

namespace App\Manager;

use App\Entity\Advertiser;
use App\Repository\AdvertiserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdvertiserManager extends AbstractManager
{
    /**
     * @var AdvertiserRepository
     */
    private $advertiserRepository;

    /**
     * AdvertiserManager constructor.
     */
    public function __construct(
        ContainerInterface $container,
        ParameterBagInterface $parameters,
        AdvertiserRepository $advertiserRepository
    ) {
        parent::__construct($container, $parameters);
        $this->advertiserRepository = $advertiserRepository;
    }

    /**
     * @return Advertiser[]
     */
    public function getAdvertisers()
    {
        return $this->advertiserRepository->findBy(['deleted' => false]);
    }

    /**
     * @return mixed
     */
    public function getAdvertiserUsers()
    {
        return $this->advertiserRepository->findActiveAdvertisers();
    }
}
