<?php declare(strict_types=1);

namespace EryseClient\Server\UserSecurity\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Client\ProfileSecurity\Exception\ResetPasswordException;
use EryseClient\Client\Token\Entity\TokenType;
use EryseClient\Client\Token\Repository\TokenRepository;
use EryseClient\Client\Token\Repository\TokenTypeRepository;
use EryseClient\Client\Token\Service\TokenService;
use EryseClient\Common\Service\MailService;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\PasswordService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserPasswordController
 *
 * @package EryseClient\Server\UserSecurity\Controller
 */
class UserPasswordController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @Route("/user/password/reset", name="user-password-request-reset")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenRepository $tokenRepository
     * @param TokenTypeRepository $tokenTypeRepository
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
        TokenTypeRepository $tokenTypeRepository
    ): Response {
        $email = $request->get('email');

        if ($email) {
            // Purge existing password request tokens
            $tokenType = $tokenTypeRepository->findType(TokenType::USER['REQUEST_RESET_PASSWORD']);
            $tokenRepository->invalidateTokens($tokenRepository->findExpiredTokens($tokenType));
            $user = $userRepository->findOneBy(['email' => $email]);

            // Check if we have this email address in DB
            if (!$user) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('request-reset-password-unknown-mail', [], 'profile')
                );
                $this->logger->notice('controller.user.security.requestResetPassword: unknown-mail', [$email]);

                return $this->redirectToRoute('user-request-reset-password');
            }

            // Check if there is already pending request
            if ($tokenService->hasUserActiveTokenType($user, $tokenType)) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('request-reset-password-already-pending', [], 'profile')
                );

                return $this->redirectToRoute('user-request-reset-password');
            }

            // Generate reset token
            $token = $tokenService->getResetPasswordToken($user);
            $tokenRepository->save($token);

            // Send email & flash info
            $mailService->sendRequestResetPasswordMail($user, $token);
            $this->addFlash('success', $this->translator->trans('request-reset-password-sent', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('User/Security/request-reset-password.html.twig');
    }

    /**
     * TODO: refactor to facade
     * @Route("/user/password/reset/{tokenHash}", name="user-password-reset")
     * @param Request $request
     * @param PasswordService $passwordService
     * @param UserRepository $userRepository
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     * @param ValidatorInterface $validator
     * @param Security $security
     * @param null $tokenHash
     *
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function resetPassword(
        Request $request,
        PasswordService $passwordService,
        UserRepository $userRepository,
        TokenService $tokenService,
        TokenRepository $tokenRepository,
        ValidatorInterface $validator,
        Security $security,
        $tokenHash = null
    ) {
        $token = $tokenRepository->findOneBy(['hash' => $tokenHash]);
        $user = null;

        if (!$token && !$security->getUser()) {
            // No token && nobody logged in
            return $this->redirectToRoute('user-request-reset-password');
        } elseif (!$token) {
            // No token but user is logged in
            $user = $security->getUser();
        } elseif ($token) {
            // Token available
            if (!$tokenService->isValid($token) || $tokenService->isExpired($token)) {
                $this->addFlash('danger', $this->translator->trans('reset-password-invalid-token', [], 'profile'));

                return $this->redirectToRoute('user-request-reset-password');
            }

            // Load user, prepare pass
            $user = $userRepository->find($token->getUserId());
        }

        if ($request->isMethod('post')) {
            try {
                $newPassword = $request->get('new-password');
                $newPasswordCheck = $request->get('new-password-check');

                // Check if password & password check are the same
                if ($newPassword !== $newPasswordCheck) {
                    throw new ResetPasswordException(
                        $this->translator->trans(
                            'reset-password-new-password-mismatch',
                            [],
                            'profile'
                        )
                    );
                }

                // Validate new password
                $errors = $validator->validatePropertyValue($user, 'plainPassword', $newPassword);
                if (count($errors)) {
                    /** @var ConstraintViolation $error */
                    foreach ($errors as $error) {
                        $this->addFlash('danger', $error->getMessage());
                    }

                    throw new ResetPasswordException();
                }

                // Make password update
                $user = $passwordService->updatePassword($newPassword, $user, $token);

                if ($token) {
                    $token->setInvalid(true);
                    $tokenRepository->save($token);
                }
                $userRepository->save($user);
            } catch (Exception $ex) {
                $message = $this->translator->trans('reset-password-failed', [], 'profile');
                $message .= $ex->getMessage() ?? ' - ' . $ex->getMessage();

                $this->addFlash(
                    'danger',
                    $message
                );
                $this->logger->error('controller.user.security.resetPassword: reset failed', [$ex, $tokenHash]);

                return $this->render('User/Security/reset-password.html.twig');
            }

            $this->addFlash('success', $this->translator->trans('reset-password-success', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('User/Security/reset-password.html.twig');
    }
}