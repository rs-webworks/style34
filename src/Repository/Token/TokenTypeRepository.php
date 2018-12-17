<?php

namespace Style34\Repository\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Style34\Entity\Token\TokenType;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenTypeRepository
 * @package Style34\Repository\Token
 * @method TokenType|null findOneBy(array $criteria, array $orderBy = null)
 */
class TokenTypeRepository extends ServiceEntityRepository implements ServiceEntityRepositoryInterface
{
    /**
     * TokenRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TokenType::class);
    }
}