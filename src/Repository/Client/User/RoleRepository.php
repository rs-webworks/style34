<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\User;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Entity\Client\Profile\Role;
use EryseClient\Repository\AbstractRepository;

/**
 * Class RoleRepository
 * @package EryseClient\Repository\Client\User
 * @method Role|null findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Role|null findOneBy(array $criteria, array $orderBy = null)
 */
class RoleRepository extends AbstractRepository
{

    /**
     * RoleRepository constructor
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Role::class);
    }
}
