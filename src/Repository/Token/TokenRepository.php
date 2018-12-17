<?php


namespace Style34\Repository\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenRepository
 * @package Style34\Repository\Profile
 * @method Token|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenRepository extends ServiceEntityRepository
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
}