<?php


namespace EryseClient\Repository\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use EryseClient\Entity\User\User;
use EryseClient\Repository\AbstractServerRepository;
use EryseClient\Utility\SaveEntityTrait;
use RaitoCZ\EryseServices\Service\ApiClientInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * Class UserRepository
 * @package EryseClient\Repository\User
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends AbstractServerRepository implements UserLoaderInterface
{
    use SaveEntityTrait;

    public function __construct(ApiClientInterface $apiClient)
    {
        parent::__construct($apiClient);
    }

    /**
     * @param string $username
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        $this->apiClient->call("user/getByUsernameOrMail");

        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
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
     * @param array $users
     */
    public function removeUsers(array $users): void
    {
        foreach($users as $user){
            $this->_em->remove($user);
        }

        $this->_em->flush();
    }
}