<?php declare(strict_types=1);

namespace EryseClient\Model\Client\Token\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use EryseClient\Model\Client\Token\Entity\TokenType;
use EryseClient\Model\Common\Repository\AbstractRepository;

/**
 * Class TokenTypeRepository
 * @package EryseClient\Repository\Token
 * @method TokenType|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenTypeRepository extends AbstractRepository implements ServiceEntityRepositoryInterface
{
    /**
     * TokenRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenType::class);
    }

    /**
     * @param string $type
     * @return TokenType|null
     */
    public function findType(string $type): ?TokenType
    {
        return $this->findOneBy(['name' => $type]);
    }
}
