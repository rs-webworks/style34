<?php declare(strict_types=1);

namespace EryseClient\Utility\EntityManager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerTrait
 * @package EryseClient\Service\Utility
 */
trait ClientEntityManagerTrait
{

    /** @var EntityManagerInterface $em */
    protected $clientEm;

    /**
     * @required
     * @param ManagerRegistry $managerRegistry
     */
    public function setClientEntityManager(ManagerRegistry $managerRegistry)
    {
        $this->clientEm = $managerRegistry->getManager('eryseClient');
    }

}