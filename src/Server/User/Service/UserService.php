<?php declare(strict_types=1);

namespace EryseClient\Server\User\Service;

use DateTime;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Server\Token\Entity\Token;
use EryseClient\Server\Token\Entity\TokenType;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Exception\ExpiredTokenException;
use EryseClient\Server\Token\Exception\InvalidTokenException;
use EryseClient\Server\Token\Repository\RememberMeTokenRepository;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Repository\TokenTypeRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\User\Entity\User;
use EryseClient\Server\User\Exception\ActivationException;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\UserRole\Entity\UserRole;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserService
 *
 * @package EryseClient\Service
 */
class UserService extends AbstractService
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /** @var UserRepository */
    protected $userRepository;

    /** @var TokenTypeRepository $tokenTypeRepository */
    private $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private $tokenRepository;

    /** @var RememberMeTokenRepository */
    private $rememberMeTokenRepository;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /** @var ProfileRepository */
    private $profileRepository;

    /** @var TokenService */
    private $tokenService;

    /** @var PasswordService  */
    private $passwordService;

    /**
     * UserService constructor.
     *
     * @param TokenTypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     * @param UserRepository $userRepository
     * @param RememberMeTokenRepository $rememberMeTokenRepository
     * @param ParameterBagInterface $parameterBag
     * @param ProfileRepository $profileRepository
     * @param TokenService $tokenService
     * @param PasswordService $passwordService
     */
    public function __construct(
        TokenTypeRepository $tokenTypeRepository,
        TokenRepository $tokenRepository,
        UserRepository $userRepository,
        RememberMeTokenRepository $rememberMeTokenRepository,
        ParameterBagInterface $parameterBag,
        ProfileRepository $profileRepository,
        TokenService $tokenService,
        PasswordService $passwordService
    ) {
        $this->tokenTypeRepository = $tokenTypeRepository;
        $this->tokenRepository = $tokenRepository;
        $this->rememberMeTokenRepository = $rememberMeTokenRepository;
        $this->parameterBag = $parameterBag;
        $this->profileRepository = $profileRepository;
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
        $this->passwordService = $passwordService;
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
        $password = $this->passwordService->encodePassword($user, $user->getPlainPassword());
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

        if ($user->getRole() === UserRole::INACTIVE) {
            $user->setRole(UserRole::VERIFIED);
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
     * TODO: Move this to rabbitMq probably?
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
                $users[] = $token->getUser();
            }

            return $users;
        } catch (Exception $ex) {
            $this->logger->error('user.expired-registration-purge-failed', [$ex]);
        }

        return null;
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
     * @return UserInterface
     */
    public function initUser(User $user): UserInterface
    {
        $profile = $this->profileRepository->findOneByUserId($user->getId());
        $profile->setUser($user);
        $user->setProfile($profile);

        return $user;
    }
}
