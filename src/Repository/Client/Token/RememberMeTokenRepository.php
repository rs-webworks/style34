<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\Token;

use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Entity\Client\Token\RememberMeToken;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\AbstractRepository;

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
