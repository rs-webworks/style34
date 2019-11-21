<?php declare(strict_types=1);

namespace EryseClient\Model\Client\ProfileRole\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Model\Client\ProfileRole\Entity\ProfileRole;
use EryseClient\Model\Common\Repository\AbstractRepository;

/**
 * Class RoleRepository
 * @package EryseClient\Repository\Client\User
 * @method ProfileRole|null findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method ProfileRole|null findOneBy(array $criteria, array $orderBy = null)
 */
class ProfileRoleRepository extends AbstractRepository
{

    /**
     * RoleRepository constructor
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfileRole::class);
    }
}
