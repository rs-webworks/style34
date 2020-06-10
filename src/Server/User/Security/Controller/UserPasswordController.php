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
use EryseClient\Server\User\Security\Facade\PasswordResetFacade;
use EryseClient\Server\User\Service\PasswordService;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
 * @Route("/user/security/password")
 */
class UserPasswordController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    private const SESSION_RESET_TOKEN = 'reset-password-token';
    private const SESSION_RESET_BY_USER = 'reset-by-user';

    /**
     * @Route("/request-reset", name="user-password-request-reset")
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
     * @Route("/reset", name="user-password-reset")
     * @param SessionInterface $session
     *
     * @return Response
     */
    public function resetPasswordViaUser(SessionInterface $session) : Response
    {
        if (!$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $session->set(self::SESSION_RESET_BY_USER, true);

        return $this->render('User/Security/reset-password.html.twig');
    }

    /**
     * @Route("/reset/via-token/{tokenHash}", name="user-password-reset-token")
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
     * @Route("/reset/set-new-password", name="user-password-set-new")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @param PasswordResetFacade $passwordResetFacade
     *
     * @return RedirectResponse|Response
     */
    public function setNewPassword(
        Request $request,
        UserRepository $userRepository,
        SessionInterface $session,
        PasswordResetFacade $passwordResetFacade
    ) {
        $user = null;
        if (!$request->isMethod('post')) {
            return $this->redirectToRoute('user-password-request-reset');
        }

        try {
            /** @var TokenEntity $token */
            $token = $session->get(self::SESSION_RESET_TOKEN);
            $resetByUser = $session->get(self::SESSION_RESET_BY_USER);

            if (!$token && !$resetByUser) {
                throw new TokenException('token: ' . $token->getHash());
            }

            $user = $resetByUser ? $this->getUser() : $userRepository->find($token->getUser());
            $passwordResetFacade->handlePasswordRequest($user, $token);
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
}
