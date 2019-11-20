<?php declare(strict_types=1);

namespace EryseClient\Client\Token\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Client\Token\Entity\RememberMeToken;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\User;

/**
 * Class RememberMeTokenRepository
 * @method RememberMeToken|null findOneBy(array $criteria, array $orderBy = null)
 */
class RememberMeTokenRepository extends AbstractRepository
{
    /**
     * RememberMeTokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RememberMeToken::class);
    }

    /**
     * @param User $user
     * @return RememberMeToken
     */
    public function findByUser(User $user): RememberMeToken
    {
        return $this->findOneBy(["username" => $user->getUsername()]);
    }
}
