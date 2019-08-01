<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Server\User\Device;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class DeviceRepository
 * @package EryseClient\Repository\Profile
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