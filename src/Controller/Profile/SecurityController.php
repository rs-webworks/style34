<?php

namespace Style34\Controller\Profile;

use Style34\Entity\Profile\Profile;
use Style34\Entity\Token\TokenType;
use Style34\Exception\Profile\ActivationException;
use Style34\Exception\Security\ResetPasswordException;
use Style34\Form\Profile\RegistrationForm;
use Style34\Repository\Profile\ProfileRepository;
use Style34\Repository\Token\TokenRepository;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Service\MailService;
use Style34\Service\ProfileService;
use Style34\Service\TokenService;
use Style34\Traits\EntityManagerTrait;
use Style34\Traits\LoggerTrait;
use Style34\Traits\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class SecurityController
 * @package Style34\Controller\Profile
 */
class SecurityController extends AbstractController
{

    use EntityManagerTrait;
    use LoggerTrait;
    use TranslatorTrait;

    /**
     * @Route("/login", name="profile-login")
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Profile/Login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="profile-logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/profile/registration", name="profile-registration")
     * @param Request $request
     * @param ProfileService $profileService
     * @param MailService $mailService
     * @param TokenService $tokenService
     * @param ProfileRepository $profileRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registration(
        Request $request,
        ProfileService $profileService,
        MailService $mailService,
        TokenService $tokenService,
        ProfileRepository $profileRepository
    ) {
        // Purge all expired & invalid requests for registration
        $profileRepository->removeProfiles($profileService->getExpiredRegistrations());

        // Load form data
        $profile = new Profile();
        $form = $this->createForm(RegistrationForm::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Prepare profile & token
                $thisIp = $request->getClientIp();
                $profile = $profileService->prepareNewProfile($profile, $thisIp);
                $token = $tokenService->getActivationToken($profile);

                // Send registration email
                $mailService->sendActivationMail($profile, $token);

                $this->em->persist($profile);
                $this->em->persist($token);
                $this->em->flush();

                // Flash & redirect
                $this->addFlash('success', $this->translator->trans('registration-success', [], 'profile'));

                return $this->redirectToRoute("profile-registration-success");
            } catch (\Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('registration-failed', [], 'profile'));
                $this->logger->error('controller.profile.security.registration: registration failed',
                    array($ex, $profile));
            }
        }

        return $this->render(
            'Profile/Security/registration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/profile/registration/activate/{tokenHash}", name="profile-registration-activate")
     * @param ProfileService $profileService
     * @param TokenRepository $tokenRepository
     * @param $tokenHash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activate(ProfileService $profileService, TokenRepository $tokenRepository, $tokenHash)
    {
        try {
            $token = $tokenRepository->findOneBy(array('hash' => $tokenHash));

            if (!$token) {
                throw new ActivationException($this->translator->trans('activation-invalid-token', [], 'profile'));
            }

            $profile = $profileService->activateProfile($token->getProfile(), $token);
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
            $this->logger->error('controller.profile.security.activate: activation failed', array($ex, $tokenHash));
        }

        return $this->render('Profile/Security/activation.html.twig');
    }

    /**
     * @Route("/profile/registration/success", name="profile-registration-success")
     */
    public function success()
    {
        return $this->render("Profile/Security/success.html.twig");
    }

    /**
     * @Route("/profile/request-reset-password", name="profile-request-reset-password")
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param TokenService $tokenService
     * @param MailService $mailService
     * @param TokenRepository $tokenRepository
     * @param TokenTypeRepository $tokenTypeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     */
    public function requestResetPassword(
        Request $request,
        ProfileRepository $profileRepository,
        TokenService $tokenService,
        MailService $mailService,
        TokenRepository $tokenRepository,
        TokenTypeRepository $tokenTypeRepository
    ) {
        $email = $request->get('email');

        if ($email) {
            // Purge existing password request tokens
            $tokenType = $tokenTypeRepository->findType(TokenType::PROFILE['REQUEST_RESET_PASSWORD']);
            $tokenRepository->invalidateTokens($tokenRepository->findExpiredTokens($tokenType));
            $profile = $profileRepository->findOneBy(array('email' => $email));

            // Check if we have this email address in DB
            if (!$profile) {
                $this->addFlash('danger',
                    $this->translator->trans('request-reset-password-unknown-mail', [], 'profile'));
                $this->logger->notice('controller.profile.security.requestResetPassword: unknown-mail', array($email));

                return $this->redirectToRoute('profile-request-reset-password');
            }

            // Check if there is already pending request
            if ($tokenService->hasProfileActiveTokenType($profile, $tokenType)) {
                $this->addFlash('danger',
                    $this->translator->trans('request-reset-password-already-pending', [], 'profile'));

                return $this->redirectToRoute('profile-request-reset-password');
            }

            // Generate reset token
            $token = $tokenService->getResetPasswordToken($profile);
            $this->em->persist($token);
            $this->em->flush();

            // Send email & flash info
            $mailService->sendRequestResetPasswordMail($profile, $token);
            $this->addFlash('success', $this->translator->trans('request-reset-password-sent', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('Profile/Security/request-reset-password.html.twig');
    }

    /**
     * @Route("/profile/reset-password/{tokenHash}", name="profile-reset-password")
     * @param Request $request
     * @param ProfileService $profileService
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     * @param ValidatorInterface $validator
     * @param Security $security
     * @param $tokenHash
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function resetPassword(
        Request $request,
        ProfileService $profileService,
        TokenService $tokenService,
        TokenRepository $tokenRepository,
        ValidatorInterface $validator,
        Security $security,
        $tokenHash = null
    ) {
        $token = $tokenRepository->findOneBy(array('hash' => $tokenHash));

        if (!$token && !$security->getUser()) {
            // No token && nobody logged in
            return $this->redirectToRoute('profile-request-reset-password');
        } elseif (!$token) {
            // No token but user is logged in
            $profile = $security->getUser();
        } elseif ($token) {
            // Token available
            if (!$tokenService->isValid($token) || $tokenService->isExpired($token)) {
                $this->addFlash('danger', $this->translator->trans('reset-password-invalid-token', [], 'profile'));

                return $this->redirectToRoute('profile-request-reset-password');
            }

            // Load profile, prepare pass
            $profile = $token->getProfile();
        }

        if ($request->isMethod('post')) {
            try {

                $newPassword = $request->get('new-password');
                $newPasswordCheck = $request->get('new-password-check');

                // Check if password & password check are the same
                if ($newPassword !== $newPasswordCheck) {
                    throw new ResetPasswordException($this->translator->trans('reset-password-new-password-mismatch',
                        [],
                        'profile'));
                }

                // Validate new password
                $errors = $validator->validatePropertyValue($profile, 'plainPassword', $newPassword);
                if (count($errors)) {
                    /** @var ConstraintViolation $error */
                    foreach ($errors as $error) {
                        $this->addFlash('danger', $error->getMessage());
                    }

                    throw new ResetPasswordException();
                }

                // Make password update
                $profile = $profileService->updatePassword($newPassword, $profile, $token);

                if ($token) {
                    $token->setInvalid(true);
                    $this->em->persist($token);
                }
                $this->em->persist($profile);
                $this->em->flush();
            } catch (\Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('reset-password-failed', [], 'profile')
                    . ($ex->getMessage() ?? ' - ' . $ex->getMessage())
                );
                $this->logger->error('controller.profile.security.resetPassword: reset failed', array($ex, $tokenHash));

                return $this->render('Profile/Security/reset-password.html.twig');
            }

            $this->addFlash('success', $this->translator->trans('reset-password-success', [], 'profile'));

            return $this->redirectToRoute('home-index');
        }

        return $this->render('Profile/Security/reset-password.html.twig');
    }

}