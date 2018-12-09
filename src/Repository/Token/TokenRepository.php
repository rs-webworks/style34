<?php


namespace Style34\Repository\Token;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Style34\Entity\Token\Token;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class TokenRepository
 * @package Style34\Repository\Profile
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
}