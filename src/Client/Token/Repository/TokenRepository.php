<?php declare(strict_types=1);

namespace EryseClient\Client\Token\Repository;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Token\Entity\Token;
use EryseClient\Client\Token\Entity\TokenType;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\User\Entity\User;
use Exception;

/**
 * Class TokenRepository
 * @package EryseClient\Repository\Token
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenRepository extends AbstractRepository
{

    /**
     * TokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    /**
     * @param TokenType $tokenType
     * @return Token[]
     * @throws Exception
     */
    public function findExpiredTokens(TokenType $tokenType)
    {
        return $this->createQueryBuilder('t')
            ->where('t.type = :tokenType')
            ->andWhere('t.expiresAt < :datetimeNow')
            ->andWhere('t.invalid = :invalid')
            ->setParameters(
                [
                    'tokenType' => $tokenType,
                    'datetimeNow' => new DateTime(),
                    'invalid' => false
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Token[] $tokens
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function invalidateTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            $token->setInvalid(true);
        }

        $this->_em->flush();
    }

    /**
     * @param User $user
     * @param TokenType $tokenType
     * @return mixed
     * @throws Exception
     */
    public function findUserValidTokensOfType(User $user, TokenType $tokenType)
    {
        return $this->createQueryBuilder('t')
            ->where('t.type = :tokenType')
            ->andWhere('t.invalid = :invalid')
            ->andWhere('t.expiresAt > :datetimeNow')
            ->andWhere('t.userId = :userId')
            ->setParameters(
                [
                    'tokenType' => $tokenType,
                    'datetimeNow' => new DateTime(),
                    'invalid' => false,
                    'userId' => $user->getId()
                ]
            )
            ->getQuery()
            ->getResult();
    }
}
