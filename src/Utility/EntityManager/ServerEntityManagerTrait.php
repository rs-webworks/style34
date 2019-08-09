<?php declare(strict_types=1);

namespace EryseClient\Utility\EntityManager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait ServerEntityManagerTrait
 * @package EryseClient\Utility
 */
trait ServerEntityManagerTrait
{

    /** @var EntityManagerInterface $em */
    protected $serverEm;

    /**
     * @required
     * @param ManagerRegistry $managerRegistry
     */
    public function setServerEntityManager(ManagerRegistry $managerRegistry)
    {
        $this->serverEm = $managerRegistry->getManager('eryseServer');
    }

}