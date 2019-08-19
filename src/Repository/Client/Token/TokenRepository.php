<?php declare(strict_types=1);

namespace EryseClient\Repository\Client\Token;

use DateTime;
use EryseClient\Entity\Client\Token\Token;
use EryseClient\Entity\Client\Token\TokenType;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\AbstractRepository;
use Exception;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenRepository
 * @package EryseClient\Repository\Token
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenRepository extends AbstractRepository
{

    /**
     * TokenRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
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
