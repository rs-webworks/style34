<?php

namespace eRyseClient\Repository\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use eRyseClient\Entity\Token\TokenType;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenTypeRepository
 * @package eRyseClient\Repository\Token
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

    /**
     * @param string $type
     * @return TokenType|null
     */
    public function findType(string $type): ?TokenType
    {
        return $this->findOneBy(array('name' => $type));
    }
}