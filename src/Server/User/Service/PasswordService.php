<?php declare(strict_types=1);

namespace EryseClient\Server\User\Service;

use EryseClient\Client\Token\Entity\Token;
use EryseClient\Client\Token\Exception\ExpiredTokenException;
use EryseClient\Client\Token\Exception\InvalidTokenException;
use EryseClient\Client\Token\Service\TokenService;
use EryseClient\Common\Service\AbstractService;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordService
 *
 * @package EryseClient\Server\User\Service
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
     * @param User $user
     * @param string $password
     *
     * @return string
     */
    public function encodePassword(User $user, string $password): string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
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
}