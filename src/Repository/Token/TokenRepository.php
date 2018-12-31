<?php


namespace eRyseClient\Repository\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Entity\Token\Token;
use eRyseClient\Entity\Token\TokenType;
use eRyseClient\Traits\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenRepository
 * @package eRyseClient\Repository\Profile
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
     * @param Profile $profile
     * @param TokenType $tokenType
     * @return mixed
     * @throws \Exception
     */
    public function findProfileValidTokensOfType(Profile $profile, TokenType $tokenType)
    {
        return $this->createQueryBuilder('t')
            ->where('t.type = :tokenType')
            ->andWhere('t.invalid = :invalid')
            ->andWhere('t.expiresAt > :datetimeNow')
            ->andWhere('t.profile = :profile')
            ->setParameters(array(
                'tokenType' => $tokenType,
                'datetimeNow' => new \DateTime(),
                'invalid' => false,
                'profile' => $profile
            ))
            ->getQuery()
            ->getResult();
    }
}