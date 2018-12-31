<?php


namespace Style34\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Style34\Entity\Profile\Device;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DeviceRepository
 * @package Style34\Repository\Profile
 */
class DeviceRepository extends ServiceEntityRepository
{
    /**
     * DeviceRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Device::class);
    }
}