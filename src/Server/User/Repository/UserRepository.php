<?php declare(strict_types=1);

namespace EryseClient\Server\User\Repository;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Client\ProfileSettings\Entity\ProfileSettings;
use EryseClient\Client\ProfileSettings\Repository\ProfileSettingsRepository;
use EryseClient\Common\Repository\AbstractRepository;
use EryseClient\Server\GoogleAuth\Entity\GoogleAuth;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\UserSettings\Entity\UserSettings;
use EryseClient\Server\UserSettings\Repository\UserSettingsRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserRepository
 *
 * @package EryseClient\Repository\Server\User
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
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
        parent::__construct($registry, User::class);
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->clientSettingsRepository = $clientSettingsRepository;
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function saveNew(User $user): void
    {
        $this->save($user);
        $this->serverSettingsRepository->save(new UserSettings($user));
        $this->clientSettingsRepository->save(new ProfileSettings($user));
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
     * @param User $user
     *
     * @return GoogleAuth|null
     */
    public function getGoogleAuthEntity(User $user): ?GoogleAuth
    {
        $settings = $this->serverSettingsRepository->findByUser($user);

        return new GoogleAuth($user, $settings);
    }
}