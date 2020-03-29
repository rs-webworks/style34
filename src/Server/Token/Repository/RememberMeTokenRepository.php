<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Repository;

use Doctrine\Persistence\ManagerRegistry;
use EryseClient\Server\Token\Entity\RememberMeTokenEntity;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\UserEntity;

/**
 * Class RememberMeTokenRepository
 * @method RememberMeTokenEntity|null findOneBy(array $criteria, array $orderBy = null)
 */
class RememberMeTokenRepository extends AbstractRepository
{
    /**
     * RememberMeTokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RememberMeTokenEntity::class);
    }

    /**
     * @param UserEntity $user
     *
     * @return RememberMeTokenEntity
     */
    public function findByUser(UserEntity $user): RememberMeTokenEntity
    {
        return $this->findOneBy(["username" => $user->getUsername()]);
    }
}
