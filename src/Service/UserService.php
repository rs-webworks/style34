<?php

namespace EryseClient\Service;

use EryseClient\Entity\User\User;
use EryseClient\Entity\User\Role;
use EryseClient\Entity\User\Settings;
use EryseClient\Entity\Token\RememberMeToken;
use EryseClient\Entity\Token\Token;
use EryseClient\Entity\Token\TokenType;
use EryseClient\Exception\User\ActivationException;
use EryseClient\Exception\Token\ExpiredTokenException;
use EryseClient\Exception\Token\InvalidTokenException;
use EryseClient\Kernel;
use EryseClient\Repository\Token\TokenRepository;
use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Utility\EntityManagerTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserService
 * @package EryseClient\Service
 */
class UserService extends AbstractService
{
    use LoggerTrait;
    use EntityManagerTrait;
    use TranslatorTrait;

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


    /**
     * UserService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenTypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     */
    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        TokenService $tokenService,
        MailService $mailService,
        TokenTypeRepository $tokenTypeRepository,
        TokenRepository $tokenRepository
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenService = $tokenService;
        $this->mailService = $mailService;
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @param User $user
     * @param string $lastIp
     * @return User
     * @throws \Exception
     */
    public function prepareNewUser(User $user, string $lastIp = '127.0.0.1')
    {
        // Get default user role
        $user->addRole(Role::INACTIVE);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // Set defaults
        $user->setCreatedAt(new \DateTime());
        $user->setLastIp($lastIp);
        $user->setRegisteredAs(serialize(array($user->getUsername(), $user->getEmail())));
        $user->setSettings(new Settings());

        return $user;
    }

    /**
     * @param User $user
     * @param Token|null $token
     * @return User|null
     * @throws ActivationException
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function activateUser(User $user, Token $token = null): ?User
    {
        if ($token) {
            if ($this->tokenService->isExpired($token)) {
                throw new ExpiredTokenException($this->translator->trans('activation-expired-token',
                    [], 'profile'));
            }

            if ($token->isInvalid()) {
                throw new InvalidTokenException($this->translator->trans('activation-invalid-token',
                    [], 'profile'));
            }
        }

        if ($user->hasRole(Role::INACTIVE)) {
            $user->addRole(Role::VERIFIED);
            $user->removeRole(Role::INACTIVE);
            $user->setActivatedAt(new \DateTime);

            return $user;
        }

        throw new ActivationException($this->translator->trans('contact-support',
            ['contactmail' => Kernel::CONTACT_MAIL], 'global'));
    }

    /**
     * @param string $newPassword
     * @param User $user
     * @param Token|null $token
     * @return User|null
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     * @throws \Exception
     */
    public function updatePassword(string $newPassword, User $user, Token $token = null): ?User
    {
        if ($token) {
            if ($this->tokenService->isExpired($token)) {
                throw new ExpiredTokenException($this->translator->trans('activation-expired-token',
                    [], 'profile'));
            }

            if ($token->isInvalid()) {
                throw new InvalidTokenException($this->translator->trans('activation-invalid-token',
                    [], 'profile'));
            }
        }

        $password = $this->passwordEncoder->encodePassword($user, $newPassword);
        $user->setPassword($password);

        return $user;
    }

    /**
     * @return User[]|null
     */
    public function getExpiredRegistrations(): ?array
    {
        try {
            $users = array();

            /** @var TokenType $tokenType */
            $tokenType = $this->tokenTypeRepository->findOneBy(array(
                'name' => TokenType::USER['ACTIVATION']
            ));

            $expiredTokens = $this->tokenRepository->findExpiredTokens($tokenType);

            /** @var Token $token */
            foreach ($expiredTokens as $token) {
                $user = $token->getUser();
                $users[] = $user;
            }

            return $users;
        } catch (\Exception $ex) {
            $this->logger->error('user.expired-registration-purge-failed', [$ex]);
        }

        return null;
    }

    /**
     * @param User $user
     * @param string $secret
     */
    public function enableTwoStepAuth(User $user, string $secret)
    {
        $user->setGoogleAuthenticatorSecret($secret);
        $settings = $user->getSettings();

        $settings->setTwoStepAuthEnabled(true);

        $this->em->persist($settings);
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    public function disableTwoStepAuth(User $user)
    {
        $settings = $user->getSettings();

        $settings->setGAuthSecret(null);
        $settings->setTwoStepAuthEnabled(false);
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);

        $this->em->persist($settings);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     */
    public function forgetDevices(User $user)
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasRememberMeToken(User $user)
    {
        $token = $this->em->getRepository(RememberMeToken::class)->findOneBy(array(
            'username' => $user->getUsername()
        ));

        return $token ? true : false;
    }

    /**
     * @param User $user
     */
    public function logoutEverywhere(User $user)
    {
        $user;
    }

}