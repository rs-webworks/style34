<?php declare(strict_types=1);
namespace EryseClient\Service;

use DateTime;
use EryseClient\Entity\Client\Token\RememberMeToken;
use EryseClient\Entity\Client\Token\Token;
use EryseClient\Entity\Client\Token\TokenType;
use EryseClient\Entity\Client\User\Role;
use EryseClient\Entity\Server\User\User;
use EryseClient\Exception\Token\ExpiredTokenException;
use EryseClient\Exception\Token\InvalidTokenException;
use EryseClient\Exception\User\ActivationException;
use EryseClient\Kernel;
use EryseClient\Repository\Client\Token\TokenRepository;
use EryseClient\Repository\Client\Token\TokenTypeRepository;
use EryseClient\Utility\EntityManagersTrait;
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
    use EntityManagersTrait;
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

    public function prepareNewUser(User $user, string $lastIp = '127.0.0.1'): User
    {
        // Get default user role
        $user->addRole(Role::INACTIVE);

        // Encode password
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);

        // Set defaults
        $user->setCreatedAt(new DateTime());
        $user->setLastIp($lastIp);
        $user->setRegisteredAs(serialize(array($user->getUsername(), $user->getEmail())));

        return $user;
    }

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

        if ($user->hasRole(Role::INACTIVE)) {
            $user->addRole(Role::VERIFIED);
            $user->removeRole(Role::INACTIVE);
            $user->setActivatedAt(new DateTime());

            return $user;
        }

        throw new ActivationException($this->translator->trans(
            'contact-support',
            ['contactmail' => Kernel::CONTACT_MAIL],
            'global'
        ));
    }

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

    public function enableTwoStepAuth(User $user, string $secret): void
    {
        $user->setGoogleAuthenticatorSecret($secret);
        $settings = $user->getSettings();

        $settings->setTwoStepAuthEnabled(true);

        $this->clientEm->persist($settings);
        $this->clientEm->flush();
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

        $this->clientEm->persist($settings);
        $this->clientEm->persist($user);
        $this->clientEm->flush();
    }

    /**
     * @param User $user
     */
    public function forgetDevices(User $user)
    {
        $user->setTrustedTokenVersion($user->getTrustedTokenVersion() + 1);
        $this->serverEm->persist($user);
        $this->serverEm->flush();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function hasRememberMeToken(User $user)
    {
        $token = $this->clientEm->getRepository(RememberMeToken::class)->findOneBy(array(
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