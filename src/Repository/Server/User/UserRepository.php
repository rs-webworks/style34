<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EryseClient\Entity\Server\User\User;
use EryseClient\Utility\SaveEntityTrait;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 * @package EryseClient\Repository\Server\User
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    use SaveEntityTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $username
     * @return mixed|null|UserInterface
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
        dump($this->createQueryBuilder('u')
                 ->where('u.username = :username OR u.email = :email')
                 ->setParameter('username', $username)
                 ->setParameter('email', $username)
                 ->getQuery()->getSQL());

        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByRole(string $role): ?User
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"' . $role . '"%');

        return $qb->getQuery()->getResult();
    }

    public function removeUsers(array $users): void
    {
        foreach ($users as $user) {
            $this->_em->remove($user);
        }

        $this->_em->flush();
    }
}