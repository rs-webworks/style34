<?php declare(strict_types=1);

namespace EryseClient\Server\User\Device\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Server\User\Device\Entity\DeviceEntity;

/**
 * Class DeviceRepository
 *
 */
class DeviceRepository extends ServiceEntityRepository
{

    /**
     * DeviceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceEntity::class);
    }
}
