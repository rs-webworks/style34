<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Entity\Server\User\Device;

/**
 * Class DeviceRepository
 * @package EryseClient\Repository\Profile
 */
class DeviceRepository extends ServiceEntityRepository
{

    /**
     * DeviceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }
}
