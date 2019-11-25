<?php declare(strict_types=1);

namespace EryseClient\Server\User\Service;

use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Token\Entity\Token;
use EryseClient\Client\Token\Entity\TokenType;
use EryseClient\Client\Token\Exception\ExpiredTokenException;
use EryseClient\Client\Token\Exception\InvalidTokenException;
use EryseClient\Client\Token\Repository\RememberMeTokenRepository;
use EryseClient\Client\Token\Repository\TokenRepository;
use EryseClient\Client\Token\Repository\TokenTypeRepository;
use EryseClient\Client\Token\Service\TokenService;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Service\MailService;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Exception\ActivationException;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\UserRole\Entity\UserRole;
use EryseClient\Server\UserSettings\Repository\UserSettingsRepository;
use Exception;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 *
 * @package EryseClient\Service
 */
class UserService extends AbstractService
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /** @var TokenService $tokenService */
    protected $tokenService;

    /** @var MailService $mailService */
    protected $mailService;

    /** @var TokenTypeRepository $tokenTypeRepository */
    private $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private $tokenRepository;

    /** @var UserSettingsRepository */
    private $serverSettingsRepository;

    /** @var UserRepository */
    private $userRepository;

    /** @var RememberMeTokenRepository */
    private $rememberMeTokenRepository;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /**
     * UserService constructor.
     *
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenTypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     * @param UserSettingsRepository $serverSettingsRepository
     * @param UserRepository $userRepository
     * @param RememberMeTokenRepository $rememberMeTokenRepository
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        MailService $mailService,
        TokenTypeRepository $tokenTypeRepository,
        TokenRepository $tokenRepository,
        UserSettingsRepository $serverSettingsRepository,
        UserRepository $userRepository,
        RememberMeTokenRepository $rememberMeTokenRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
        $this->serverSettingsRepository = $serverSettingsRepository;
        $this->userRepository = $userRepository;
        $this->rememberMeTokenRepository = $rememberMeTokenRepository;
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param User $user
     * @param string $lastIp
     *
     * @return User
     * @throws Exception
     */
    public function prepareNewUser(User $user, string $lastIp = '127.0.0.1'): User
    {
        // Get default user role
        $user->setRole(UserRole::INACTIVE);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // Set defaults
        $user->setCreatedAt(new DateTime());
        $user->setLastIp($lastIp);
        $user->setRegisteredAs(serialize([$user->getUsername(), $user->getEmail()]));

        return $user;
    }

    /**
     * @param User $user
     * @param Token|null $token
     *
     * @return User|null
     * @throws ActivationException
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     */
    public function activateUser(User $user, Token $token = null): ?User
    {
        if ($token) {
            if ($this->tokenService->isExpired($token)) {
                throw new ExpiredTokenException($this->translator->trans('activation-expired-token', [], 'profile'));
            }

            if ($token->isInvalid()) {
                throw new InvalidTokenException($this->translator->trans('activation-invalid-token', [], 'profile'));
            }
        }

        if ($user->getRole() === Role::INACTIVE) {
            $user->setRole(Role::VERIFIED);
            $user->setActivatedAt(new DateTime());

            return $user;
        }

        throw new ActivationException(
            $this->translator->trans(
                'contact-support',
                ['contact-mail' => $this->parameterBag->get("eryseClient.emails.admin")],
                'global'
            )
        );
    }

    /**
     * @param string $newPassword
     * @param User $user
     * @param Token|null $token
     *
     * @return User|null
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     */
    public function updatePassword(string $newPassword, User $user, Token $token = null): ?User
    {
        if ($token) {
            if ($this->tokenService->isExpired($token)) {
                throw new ExpiredTokenException($this->translator->trans('activation-expired-token', [], 'profile'));
            }

            if ($token->isInvalid()) {
                throw new InvalidTokenException($this->translator->trans('activation-invalid-token', [], 'profile'));
            }
        }

        $password = $this->passwordEncoder->encodePassword($user, $newPassword);
        $user->setPassword($password);

        return $user;
    }

    /**
     * @return array|null
     */
    public function getExpiredRegistrations(): ?array
    {
        try {
            $users = [];

            /** @var TokenType $tokenType */
            $tokenType = $this->tokenTypeRepository->findOneBy(
                [
                    'name' => TokenType::USER['ACTIVATION'],
                ]
            );

            $expiredTokens = $this->tokenRepository->findExpiredTokens($tokenType);

            foreach ($expiredTokens as $token) {
                $users[] = $this->userRepository->find($token->getUserId());
            }

            return $users;
        } catch (Exception $ex) {
            $this->logger->error('user.expired-registration-purge-failed', [$ex]);
        }

        return null;
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
    public function forgetDevices(User $user): void
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasRememberMeToken(User $user): bool
    {
        $token = $this->rememberMeTokenRepository->findByUser($user);

        return $token ? true : false;
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
}
