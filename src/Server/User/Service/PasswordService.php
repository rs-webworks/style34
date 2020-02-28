<?php declare(strict_types=1);

namespace EryseClient\Server\User\Service;

use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Exception\ExpiredTokenException;
use EryseClient\Server\Token\Exception\InvalidTokenException;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\User\Entity\UserEntity;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordService
 *
 *
 */
class PasswordService extends AbstractService
{
    use TranslatorAwareTrait;

    /** @var TokenService */
    protected $tokenService;

    /** @var UserPasswordEncoderInterface $passwordEncoder */
    protected $passwordEncoder;

    /**
     * PasswordService constructor.
     *
     * @param TokenService $tokenService
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(TokenService $tokenService, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->tokenService = $tokenService;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param UserEntity $user
     * @param string $password
     *
     * @return string
     */
    public function encodePassword(UserEntity $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }

    /**
     * @param string $newPassword
     * @param UserEntity $user
     * @param TokenEntity|null $token
     *
     * @return UserEntity|null
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     */
    public function updatePassword(string $newPassword, UserEntity $user, TokenEntity $token = null): ?UserEntity
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
}
