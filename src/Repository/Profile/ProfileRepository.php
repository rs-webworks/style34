<?php


namespace Style34\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Style34\Entity\Profile\Profile;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Class ProfileRepository
 * @package Style34\Repository\Profile
 */
class ProfileRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * ProfileRepository constructor
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('p')
            ->where('p.username = :username OR p.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }
}