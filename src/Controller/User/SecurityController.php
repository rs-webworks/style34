<?php declare(strict_types=1);

namespace EryseClient\Controller\User;

use EryseClient\Entity\Client\Token\TokenType;
use EryseClient\Entity\Server\User\User;
use EryseClient\Exception\Security\ResetPasswordException;
use EryseClient\Exception\User\ActivationException;
use EryseClient\Form\User\RegistrationForm;
use EryseClient\Repository\Client\Token\TokenRepository;
use EryseClient\Repository\Client\Token\TokenTypeRepository;
use EryseClient\Repository\Server\User\UserRepository;
use EryseClient\Service\MailService;
use EryseClient\Service\TokenService;
use EryseClient\Service\UserService;
use EryseClient\Utility\EntityManagerTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SecurityController
 * @package EryseClient\Controller\User
 */
class SecurityController extends AbstractController
{

    use EntityManagerTrait;
    use LoggerTrait;
    use TranslatorTrait;

    /**
     * @Route("/login", name="security-login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'User/Login/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/logout", name="security-logout")
     */
    public function logout()
    {
    }


    /**
     * @Route("/user/registration", name="user-registration")
     */
    public function registration(
        Request $request,
        UserService $userService,
        MailService $mailService,
        TokenService $tokenService,
        UserRepository $userRepository
    ) {
        // Purge all expired & invalid requests for registration
        if ($expiredRegistrations = $userService->getExpiredRegistrations()) {
            $userRepository->removeUsers($expiredRegistrations);
        }


        // Load form data
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Prepare user & token
                $thisIp = $request->getClientIp();
                $user = $userService->prepareNewUser($user, $thisIp);
                $token = $tokenService->getActivationToken($user);

                // Send registration email
                $mailService->sendActivationMail($user, $token);

                $userRepository->saveNew($user);
                $this->em->persist($token);
                $this->em->flush();

                // Flash & redirect
                $this->addFlash('success', $this->translator->trans('registration-success', [], 'profile'));

                return $this->redirectToRoute("user-registration-success");
            } catch (\Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('registration-failed', [], 'profile'));
                $this->logger->error(
                    'controller.user.security.registration: registration failed',
                    array($ex, $user)
                );
            }
        }

        return $this->render(
            'User/Security/registration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/user/registration/activate/{tokenHash}", name="user-registration-activate")
     */
    public function activate(UserService $userService, TokenRepository $tokenRepository, $tokenHash)
    {
        try {
            $token = $tokenRepository->findOneBy(array('hash' => $tokenHash));

            if (!$token) {
                throw new ActivationException($this->translator->trans('activation-invalid-token', [], 'profile'));
            }

            $profile = $userService->activateUser($token->getUser(), $token);
            $token->setInvalid(true);

            $this->em->persist($profile);
            $this->em->persist($token);
            $this->em->flush();

            $this->addFlash('success', $this->translator->trans('activation-success', [], 'profile'));
        } catch (\Exception $ex) {
            $this->addFlash(
                'danger',
                $this->translator->trans('activation-failed', [], 'profile') . ' - ' . $ex->getMessage()
            );
            $this->logger->error('controller.user.security.activate: activation failed', array($ex, $tokenHash));
        }

        return $this->render('User/Security/activation.html.twig');
    }

    /**
     * @Route("/user/registration/success", name="user-registration-success")
     */
    public function success()
    {
        return $this->render("User/Security/success.html.twig");
    }

    /**
     * @Route("/user/request-reset-password", name="user-request-reset-password")
     */
    public function requestResetPassword(
        Request $request,
        UserRepository $userRepository,
        TokenService $tokenService,
        MailService $mailService,
        TokenRepository $tokenRepository,
        TokenTypeRepository $tokenTypeRepository
    ) {
        $email = $request->get('email');

        if ($email) {
            // Purge existing password request tokens
            $tokenType = $tokenTypeRepository->findType(TokenType::USER['REQUEST_RESET_PASSWORD']);
            $tokenRepository->invalidateTokens($tokenRepository->findExpiredTokens($tokenType));
            $user = $userRepository->findOneBy(array('email' => $email));

            // Check if we have this email address in DB
            if (!$user) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('request-reset-password-unknown-mail', [], 'profile')
                );
                $this->logger->notice('controller.user.security.requestResetPassword: unknown-mail', array($email));

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
            $this->em->persist($token);
            $this->em->flush();

            // Send email & flash info
            $mailService->sendRequestResetPasswordMail($user, $token);
            $this->addFlash('success', $this->translator->trans('request-reset-password-sent', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('User/Security/request-reset-password.html.twig');
    }

    /**
     * @Route("/user/reset-password/{tokenHash}", name="user-reset-password")
     */
    public function resetPassword(
        Request $request,
        UserService $userService,
        TokenService $tokenService,
        TokenRepository $tokenRepository,
        ValidatorInterface $validator,
        Security $security,
        $tokenHash = null
    ) {
        $token = $tokenRepository->findOneBy(array('hash' => $tokenHash));

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
            $user = $token->getUser();
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
                $user = $userService->updatePassword($newPassword, $user, $token);

                if ($token) {
                    $token->setInvalid(true);
                    $this->em->persist($token);
                }
                $this->em->persist($user);
                $this->em->flush();
            } catch (\Exception $ex) {
                $this->addFlash(
                    'danger',
                    $this->translator->trans('reset-password-failed', [], 'profile') . ($ex->getMessage(
                        ) ?? ' - ' . $ex->getMessage())
                );
                $this->logger->error('controller.user.security.resetPassword: reset failed', array($ex, $tokenHash));

                return $this->render('User/Security/reset-password.html.twig');
            }

            $this->addFlash('success', $this->translator->trans('reset-password-success', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('User/Security/reset-password.html.twig');
    }

}
