<?php

namespace Style34\Controller\Profile;

use Style34\Entity\Profile\Profile;
use Style34\Exception\Profile\ActivationException;
use Style34\Exception\Security\ResetPasswordException;
use Style34\Form\Profile\RegistrationForm;
use Style34\Repository\Profile\ProfileRepository;
use Style34\Repository\Token\TokenRepository;
use Style34\Service\MailService;
use Style34\Service\ProfileService;
use Style34\Service\TokenService;
use Style34\Traits\EntityManagerTrait;
use Style34\Traits\LoggerTrait;
use Style34\Traits\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

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
                $this->logger->error('controller.profile.index: registration failed', array($ex, $profile));
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
            $this->logger->error('controller.profile.activate: activation failed', array($ex, $tokenHash));
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
     * @param ProfileService $profileService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function requestResetPassword(
        Request $request,
        ProfileRepository $profileRepository,
        ProfileService $profileService
    ) {
        $email = $request->get('email');
        $profile = $profileRepository->findOneBy(array('email' => $email));

        if(!$profile){
            $this->addFlash('danger', $this->translator->trans('request-reset-password-unknown-mail', [], 'profile'));
            $this->logger->notice('profile.request-reset-password-unknown-mail', array($email));
        }

        return $this->render('Profile/Security/request-reset-password.html.twig');
    }

    /**
     * @Route("/profile/reset-password/{tokenHash}", name="profile-reset-password")
     * @param Request $request
     * @param ProfileRepository $profileRepository
     * @param ProfileService $profileService
     * @param $tokenHash
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resetPassword(
        Request $request,
        ProfileRepository $profileRepository,
        ProfileService $profileService,
        $tokenHash = null
    ) {
        // TODO: If tokenHash == null then check if user is logged in, then present him with password change form
        if(!$tokenHash){
            return $this->redirectToRoute('profile-request-reset-password');
        }
    }

}