<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\Profile\Security\Exception\ResetPasswordException;
use EryseClient\Common\Entity\FlashType;
use EryseClient\Common\Service\MailService;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Entity\TokenEntity;
use EryseClient\Server\Token\Exception\TokenException;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\Token\Type\Entity\TypeEntity;
use EryseClient\Server\Token\Type\Repository\TypeRepository;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\PasswordService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserPasswordController
 * @Route("/user/security")
 */
class UserPasswordController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    protected const SESSION_RESET_TOKEN = 'reset-password-token';

    /**
     * @Route("/password/request-reset", name="user-password-request-reset")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenRepository $tokenRepository
     * @param TypeRepository $tokenTypeRepository
     *
     * @return RedirectResponse|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function requestResetPassword(
        Request $request,
        UserRepository $userRepository,
        TokenService $tokenService,
        MailService $mailService,
        TokenRepository $tokenRepository,
        TypeRepository $tokenTypeRepository
    ) : Response {
        $email = $request->get('email');

        if ($email) {
            // Purge existing password request tokens
            $tokenType = $tokenTypeRepository->findType(TypeEntity::USER['REQUEST_RESET_PASSWORD']);
            $tokenRepository->invalidateTokens($tokenRepository->findExpiredTokens($tokenType));
            $user = $userRepository->findOneBy(['email' => $email]);

            // Check if we have this email address in DB
            if (!$user) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('request-reset-password-unknown-mail', [], 'profile')
                );
                $this->logger->notice('controller.user.security.requestResetPassword: unknown-mail', [$email]);

                return $this->redirectToRoute('user-password-request-reset');
            }

            // Check if there is already pending request
            if ($tokenService->hasUserActiveTokenType($user, $tokenType)) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('request-reset-password-already-pending', [], 'profile')
                );

                return $this->redirectToRoute('user-password-request-reset');
            }

            // Generate reset token
            $token = $tokenService->generateResetPasswordToken($user);
            $tokenRepository->save($token);

            // Send email & flash info
            $mailService->sendRequestResetPasswordMail($user, $token);
            $this->addFlash('success', $this->translator->trans('request-reset-password-sent', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('User/Security/request-reset-password.html.twig');
    }

    /**
     * @IsGranted(EryseClient\Server\User\Role\Entity\RoleEntity::USER)
     * @Route("/password/reset", name="user-password-reset")
     */
    public function resetPasswordViaUser() : Response
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('User/Security/reset-password.html.twig');
    }

    /**
     * @Route("/password/reset/{tokenHash}", name="user-password-reset-token")
     *
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     * @param Session $session
     * @param null $tokenHash
     *
     * @return RedirectResponse|Response
     */
    public function resetPasswordViaToken(
        TokenService $tokenService,
        TokenRepository $tokenRepository,
        Session $session,
        $tokenHash = null
    ) {
        $token = $tokenRepository->findOneBy(['hash' => $tokenHash]);

        try {
            $tokenService->verifyToken($token, TypeEntity::USER['REQUEST_RESET_PASSWORD']);
        } catch (TokenException $exception) {
            $this->logger->info('Token verification failed', [$exception]);
            $this->addFlash(FlashType::DANGER, $this->translator->trans('reset-password-invalid-token', [], 'profile'));

            return $this->redirectToRoute('user-password-request-reset');
        }

        $session->set(self::SESSION_RESET_TOKEN, $token);

        return $this->render('User/Security/reset-password.html.twig');
    }

    /**
     * @Route("/password/reset/set-new-password", name="user-password-set-new")
     * @param Request $request
     *
     * @param ValidatorInterface $validator
     * @param TokenRepository $tokenRepository
     * @param TokenService $tokenService
     * @param UserRepository $userRepository
     * @param PasswordService $passwordService
     * @param Session $session
     *
     * @return RedirectResponse|Response
     */
    public function setNewPassword(
        Request $request,
        ValidatorInterface $validator,
        TokenRepository $tokenRepository,
        TokenService $tokenService,
        UserRepository $userRepository,
        PasswordService $passwordService,
        Session $session
    ) {
        if (!$request->isMethod('post')) {
            return $this->redirectToRoute('user-password-request-reset');
        }
        $user = null;

        try {
            /** @var TokenEntity $token */
            $token = $session->get(self::SESSION_RESET_TOKEN);

            if (!$token) {
                throw new TokenException('token: ' . $token->getHash());
            }

            $user = $userRepository->find($token->getUser());

            $newPassword = $request->get('new-password');
            $newPasswordCheck = $request->get('new-password-check');

            $this->validateNewPassword($validator, $user, $newPassword, $newPasswordCheck);

            // Make password update
            $user = $passwordService->updatePassword($newPassword, $user, $token);

            $tokenRepository->save($tokenService->invalidate($token));
            $userRepository->save($user);
        } catch (Exception $ex) {
            $message = $this->translator->trans('reset-password-failed', [], 'profile');
            $message .= $ex->getMessage() ?? ' - ' . $ex->getMessage();

            $this->addFlash('danger', $message);
            $this->logger->error('User password reset failed', [$ex, $token, $user]);

            return $this->render('User/Security/reset-password.html.twig');
        }

        $this->addFlash('success', $this->translator->trans('reset-password-success', [], 'profile'));

        return $this->redirectToRoute('home-index');
    }

    /**
     * @param ValidatorInterface $validator
     * @param UserInterface $user
     * @param string $newPassword
     * @param string $verify
     *
     * @throws ResetPasswordException
     */
    private function validateNewPassword(
        ValidatorInterface $validator,
        UserInterface $user,
        string $newPassword,
        string $verify
    ) : void {
        if ($newPassword !== $verify) {
            throw new ResetPasswordException(
                $this->translator->trans('reset-password-new-password-mismatch', [], 'profile')
            );
        }

        $errors = $validator->validatePropertyValue($user, 'plainPassword', $newPassword);
        if (count($errors)) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $this->addFlash('danger', $error->getMessage());
            }

            throw new ResetPasswordException('Password validation failed');
        }
    }
}
