<?php declare(strict_types=1);

namespace EryseClient\Server\User\Service;

use DateTime;
use EryseClient\Client\Profile\Repository\ProfileRepository;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Server\Token\Exception\ExpiredTokenException;
use EryseClient\Server\Token\Exception\InvalidTokenException;
use EryseClient\Server\Token\Repository\RememberMeTokenRepository;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\Token\Type\Entity\TypeEntity;
use EryseClient\Server\Token\Type\Repository\TypeRepository;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Exception\ActivationException;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Role\Entity\RoleEntity as UserRole;
use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserService
 *
 *
 */
class UserService extends AbstractService
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /** @var UserRepository */
    protected UserRepository $userRepository;

    /** @var TypeRepository $tokenTypeRepository */
    private TypeRepository $tokenTypeRepository;

    /** @var TokenRepository $tokenRepository */
    private TokenRepository $tokenRepository;

    /** @var RememberMeTokenRepository */
    private RememberMeTokenRepository $rememberMeTokenRepository;

    /** @var ParameterBagInterface */
    private ParameterBagInterface $parameterBag;

    /** @var ProfileRepository */
    private ProfileRepository $profileRepository;

    /** @var TokenService */
    private TokenService $tokenService;

    /** @var PasswordService  */
    private PasswordService $passwordService;

    /**
     * UserService constructor.
     *
     * @param TypeRepository $tokenTypeRepository
     * @param TokenRepository $tokenRepository
     * @param UserRepository $userRepository
     * @param RememberMeTokenRepository $rememberMeTokenRepository
     * @param ParameterBagInterface $parameterBag
     * @param ProfileRepository $profileRepository
     * @param TokenService $tokenService
     * @param PasswordService $passwordService
     */
    public function __construct(
        TypeRepository $tokenTypeRepository,
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
     * @param UserEntity $user
     * @param TokenEntity|null $token
     *
     * @return UserEntity|null
     * @throws ActivationException
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     */
    public function activateUser(UserEntity $user, TokenEntity $token = null): ?UserEntity
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
                ['contact-mail' => $this->parameterBag->get('eryseClient.emails.admin')],
                'global'
            )
        );
    }


    /**
     * @return array|null
     */
    public function findExpiredRegistrations(): ?array
    {
        try {
            $users = [];

            /** @var TypeEntity $tokenType */
            $tokenType = $this->tokenTypeRepository->findOneBy(
                [
                    'name' => TypeEntity::USER['ACTIVATION'],
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
     * @param UserEntity $user
     *
     * @return bool
     */
    public function hasRememberMeToken(UserEntity $user): bool
    {
        $token = $this->rememberMeTokenRepository->findByUser($user);

        return $token ? true : false;
    }


    /**
     * @param UserEntity $user
     *
     * @return UserInterface
     */
    public function initUser(UserEntity $user): UserInterface
    {
        $profile = $this->profileRepository->findOneByUserId($user->getId());
        $profile->setUser($user);
        $user->setProfile($profile);

        return $user;
    }
}
