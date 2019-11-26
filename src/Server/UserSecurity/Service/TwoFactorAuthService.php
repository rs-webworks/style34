<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\UserSettings\Repository\UserSettingsRepository;

/**
 * Class TwoFactorAuthService
 *
 * @package EryseClient\Server\User\Service
 */
class TwoFactorAuthService
{
    /** @var UserSettingsRepository */
    protected $serverSettingsRepository;

    /** @var UserRepository */
    protected $userRepository;

    /**
     * TwoFactorAuthService constructor.
     *
     * @param UserSettingsRepository $serverSettingsRepository
     * @param UserRepository $userRepository
     */
    public function __construct(UserSettingsRepository $serverSettingsRepository, UserRepository $userRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @param string $secret
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function enableTwoStepAuth(User $user, string $secret): void
    {
        $settings = $this->serverSettingsRepository->findByUser($user);
        $settings->setGAuthSecret($secret);
        $settings->setTwoStepAuthEnabled(true);

        $this->serverSettingsRepository->save($settings);
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function disableTwoStepAuth(User $user): void
    {
        $settings = $this->serverSettingsRepository->findByUser($user);
        $settings->setGAuthSecret(null);
        $settings->setTwoStepAuthEnabled(false);

        $this->serverSettingsRepository->save($settings);

        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function logoutEverywhere(User $user): void
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function forgetDevices(User $user): void
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }
}