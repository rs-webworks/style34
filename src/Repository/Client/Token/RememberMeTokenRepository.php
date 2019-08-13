<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\Token\RememberMeToken;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class RememberMeTokenRepository
 * @method RememberMeToken|null findOneBy(array $criteria, array $orderBy = null)
 */
class RememberMeTokenRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RememberMeToken::class);
    }

    public function findByUser(User $user): RememberMeToken
    {
        return $this->findOneBy(["username" => $user->getUsername()]);
    }

}