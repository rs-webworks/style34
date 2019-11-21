<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserDevice\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Model\Server\UserDevice\Entity\UserDevice;

/**
 * Class DeviceRepository
 * @package EryseClient\Repository\Profile
 */
class UserDeviceRepository extends ServiceEntityRepository
{

    /**
     * DeviceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserDevice::class);
    }
}
