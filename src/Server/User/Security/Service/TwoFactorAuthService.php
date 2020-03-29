<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Exception\UserException;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Settings\Repository\SettingsRepository;

/**
 * Class TwoFactorAuthService
 *
 *
 */
class TwoFactorAuthService
{
    /** @var SettingsRepository */
    protected SettingsRepository $serverSettingsRepository;

    /** @var UserRepository */
    protected UserRepository $userRepository;

    /**
     * TwoFactorAuthService constructor.
     *
     * @param SettingsRepository $serverSettingsRepository
     * @param UserRepository $userRepository
     */
    public function __construct(SettingsRepository $serverSettingsRepository, UserRepository $userRepository)
    {
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserEntity $user
     * @param string $secret
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserException
     */
    public function enableTwoStepAuth(UserEntity $user, string $secret): void
    {
        $settings = $this->serverSettingsRepository->findByUser($user);
        $settings->setGAuthSecret($secret);
        $settings->setTwoStepAuthEnabled(true);

        $this->serverSettingsRepository->save($settings);
    }

    /**
     * @param UserEntity $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws UserException
     */
    public function disableTwoStepAuth(UserEntity $user): void
    {
        $settings = $this->serverSettingsRepository->findByUser($user);
        $settings->setGAuthSecret(null);
        $settings->setTwoStepAuthEnabled(false);

        $this->serverSettingsRepository->save($settings);

        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }

    /**
     * @param UserEntity $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function logoutEverywhere(UserEntity $user): void
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }

    /**
     * @param UserEntity $user
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function forgetDevices(UserEntity $user): void
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }
}
