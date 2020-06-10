<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Facade;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Security\Exception\ResetPasswordException;
use EryseClient\Common\Token\TokenInterface;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Server\Token\Exception\ExpiredTokenException;
use EryseClient\Server\Token\Exception\InvalidTokenException;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\PasswordService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PasswordResetFacade
 */
class PasswordResetFacade
{
    use TranslatorAwareTrait;

    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var TokenRepository
     */
    private TokenRepository $tokenRepository;
    /**
     * @var TokenService
     */
    private TokenService $tokenService;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;
    /**
     * @var PasswordService
     */
    private PasswordService $passwordService;
    /**
     * @var SessionInterface
     */
    private SessionInterface $session;

    /**
     * PasswordResetFacade constructor.
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param TokenRepository $tokenRepository
     * @param TokenService $tokenService
     * @param UserRepository $userRepository
     * @param PasswordService $passwordService
     * @param SessionInterface $session
     */
    public function __construct(
        Request $request,
        ValidatorInterface $validator,
        TokenRepository $tokenRepository,
        TokenService $tokenService,
        UserRepository $userRepository,
        PasswordService $passwordService,
        SessionInterface $session
    ) {
        $this->request = $request;
        $this->tokenRepository = $tokenRepository;
        $this->tokenService = $tokenService;
        $this->userRepository = $userRepository;
        $this->passwordService = $passwordService;
    }

    /**
     * @param UserEntity $user
     * @param TokenEntity $token
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws ExpiredTokenException
     * @throws InvalidTokenException
     */
    public function handlePasswordRequest(UserEntity $user, TokenEntity $token) : void
    {
        $newPassword = $this->request->get('new-password');
        $newPasswordCheck = $this->request->get('new-password-check');

        // Make password update
        $user = $this->passwordService->updatePassword($newPassword, $user, $token);

        if ($token) {
            $this->tokenRepository->save($this->tokenService->invalidate($token));
        }
        $this->userRepository->save($user);
    }
}
