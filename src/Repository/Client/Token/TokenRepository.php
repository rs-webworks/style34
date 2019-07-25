<?php


namespace EryseClient\Repository\Client\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\Client\Token\Token;
use EryseClient\Entity\Client\Token\TokenType;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenRepository
 * @package EryseClient\Repository\Token
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenRepository extends ServiceEntityRepository
{
    use SaveEntityTrait;

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
     * @return mixed
     * @throws \Exception
     */
    public function findExpiredTokens(TokenType $tokenType)
    {
        return $this->createQueryBuilder('t')
            ->where('t.type = :tokenType')
            ->andWhere('t.expiresAt < :datetimeNow')
            ->andWhere('t.invalid = :invalid')
            ->setParameters(array(
                'tokenType' => $tokenType,
                'datetimeNow' => new \DateTime(),
                'invalid' => false
            ))
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
     * @throws \Exception
     */
    public function findUserValidTokensOfType(User $user, TokenType $tokenType)
    {
        return $this->createQueryBuilder('t')
            ->where('t.type = :tokenType')
            ->andWhere('t.invalid = :invalid')
            ->andWhere('t.expiresAt > :datetimeNow')
            ->andWhere('t.user = :user')
            ->setParameters(array(
                'tokenType' => $tokenType,
                'datetimeNow' => new \DateTime(),
                'invalid' => false,
                'user' => $user
            ))
            ->getQuery()
            ->getResult();
    }
}