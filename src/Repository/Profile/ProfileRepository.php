<?php


namespace eRyseClient\Repository\Profile;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use eRyseClient\Entity\Profile\Profile;
use eRyseClient\Traits\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Class ProfileRepository
 * @package eRyseClient\Repository\Profile
 * @method Profile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profile|null find($id, $lockMode = null, $lockVersion = null)
 */
class ProfileRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    use SaveEntityTrait;

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

    /**
     * @param $role
     * @return mixed
     */
    public function findByRole($role){
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array $profiles
     */
    public function removeProfiles(array $profiles): void
    {
        foreach($profiles as $profile){
            $this->_em->remove($profile);
        }

        $this->_em->flush();
    }
}