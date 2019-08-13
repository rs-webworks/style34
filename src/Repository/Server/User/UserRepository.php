<?php declare(strict_types=1);

namespace EryseClient\Repository\Server\User;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use EryseClient\Entity\Client\User\ClientSettings;
use EryseClient\Entity\Server\User\GoogleAuth;
use EryseClient\Entity\Server\User\ServerSettings;
use EryseClient\Entity\Server\User\User;
use EryseClient\Repository\Client\User\ClientSettingsRepository;
use EryseClient\Repository\Client\User\SettingsRepository;
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

    /** @var ServerSettingsRepository */
    private $serverSettingsRepository;

    /** @var ClientSettingsRepository */
    private $clientSettingsRepository;

    public function __construct(
        RegistryInterface $registry,
        ServerSettingsRepository $serverSettingsRepository,
        ClientSettingsRepository $clientSettingsRepository
    ) {
        parent::__construct($registry, User::class);
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->clientSettingsRepository = $clientSettingsRepository;
    }


    public function saveNew(User $user): void
    {
        $this->save($user);
        $this->serverSettingsRepository->save(new ServerSettings($user));
        $this->clientSettingsRepository->save(new ClientSettings($user));
    }

    /**
     * @param string $username
     * @return mixed|null|UserInterface
     * @throws NonUniqueResultException
     */
    public function loadUserByUsername($username)
    {
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

        return $qb->getQuery()
            ->getResult();
    }

    public function removeUsers(array $users): void
    {
        foreach ($users as $user) {
            $this->_em->remove($user);
        }

        $this->_em->flush();
    }

    public function getGoogleAuthEntity(User $user): ?GoogleAuth
    {
        $settings = $this->serverSettingsRepository->findByUser($user);

        return new GoogleAuth($user, $settings);
    }

}