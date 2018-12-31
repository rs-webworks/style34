<?php


namespace eRyseClient\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use eRyseClient\Entity\Profile\Device;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DeviceRepository
 * @package eRyseClient\Repository\Profile
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