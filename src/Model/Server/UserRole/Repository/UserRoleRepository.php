<?php declare(strict_types=1);

namespace EryseClient\Model\Server\UserRole\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Model\Common\Repository\AbstractRepository;
use EryseClient\Model\Server\UserRole\Entity\UserRole;

/**
 * Class UserRoleRepository
 * @package EryseClient\Model\Server\UserRole\Repository
 * @method UserRole findOneBy(array $criteria, array $orderBy = null)
 */
class UserRoleRepository extends AbstractRepository
{
    /**
     * RoleRepository constructor
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserRole::class);
    }
}
