<?php declare(strict_types=1);

namespace EryseClient\Server\User\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Settings\Entity\SettingsEntity as ProfileSettingsEntity;
use EryseClient\Client\Profile\Settings\Repository\SettingsRepository as ProfileSettingsRepository;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\GoogleAuth\Entity\GoogleAuthEntity;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Settings\Entity\SettingsEntity as UserSettingsEntity;
use EryseClient\Server\User\Settings\Repository\SettingsRepository as UserSettingsRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 *
 *
 * @method UserEntity|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEntity|null find($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends AbstractRepository implements UserLoaderInterface
{

    /** @var UserSettingsRepository */
    private $serverSettingsRepository;

    /** @var ProfileSettingsRepository */
    private $clientSettingsRepository;


    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     * @param UserSettingsRepository $serverSettingsRepository
     * @param ProfileSettingsRepository $clientSettingsRepository
     */
    public function __construct(
        ManagerRegistry $registry,
        UserSettingsRepository $serverSettingsRepository,
        ProfileSettingsRepository $clientSettingsRepository
    ) {
        parent::__construct($registry, UserEntity::class);
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->clientSettingsRepository = $clientSettingsRepository;
    }

    /**
     * @param UserEntity $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveNew(UserEntity $user): void
    {
        $this->save($user);
        $this->serverSettingsRepository->save(new UserSettingsEntity($user));
        $this->clientSettingsRepository->save(new ProfileSettingsEntity($user));
    }

    /**
     * This metod just implements interface, in order to obtain fully loaded user with all its entities call
     * loadUserById
     *
     * @param string $username
     *
     * @return mixed|null|UserInterface
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @see loadUserById
     *
     */
    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param string $username
     *
     * @return UserEntity
     */
    public function findOneByUsername(string $username): UserEntity
    {
        return $this->findOneBy(["username" => $username]);
    }

    /**
     * @param string $role
     *
     * @return array|null
     */
    public function findByRole(string $role): ?array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.role = :role')
            ->setParameter('role', $role);

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * @param array $users
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeUsers(array $users): void
    {
        foreach ($users as $user) {
            $this->_em->remove($user);
        }

        $this->_em->flush();
    }

    /**
     * @param UserEntity $user
     *
     * @return GoogleAuthEntity|null
     */
    public function getGoogleAuthEntity(UserEntity $user): ?GoogleAuthEntity
    {
        $settings = $this->serverSettingsRepository->findByUser($user);

        return new GoogleAuthEntity($user, $settings);
    }
}
