<?php declare(strict_types=1);

namespace EryseClient\Server\Token\Repository;

use DateTime;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Server\Token\Type\Entity\TypeEntity;
use EryseClient\Server\User\Entity\UserEntity;
use Exception;

/**
 * Class TokenRepository
 *
 *
 * @method TokenEntity|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenRepository extends AbstractRepository
{

    /**
     * TokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenEntity::class);
    }

    /**
     * @param TypeEntity $tokenType
     *
     * @return TokenEntity[]
     * @throws Exception
     */
    public function findExpiredTokens(TypeEntity $tokenType)
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
     * @param TokenEntity[] $tokens
     *
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
     * @param UserEntity $user
     * @param TypeEntity $tokenType
     *
     * @return mixed
     * @throws Exception
     */
    public function findUserValidTokensOfType(UserEntity $user, TypeEntity $tokenType)
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
