<?php

namespace Style34\Controller\Profile;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Style34\Entity\Profile\Profile;
use Style34\Form\Profile\SettingsForm;
use Style34\Repository\Profile\ProfileRepository;
use Style34\Service\ProfileService;
use Style34\Traits\EntityManagerTrait;
use Style34\Traits\LoggerTrait;
use Style34\Traits\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ProfileController
 * @package Style34\Controller\Profile
 * @IsGranted(Style34\Entity\Profile\Role::MEMBER)
 */
class ProfileController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagerTrait;

    /**
     * @Route("/profile/view/{username}", name="profile-view")
     * @param ProfileRepository $profileRepository
     * @param null $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(ProfileRepository $profileRepository, $username = null)
    {
        if ($username == null) {
            $username = $this->getUser()->getUsername();
        }

        try {
            $profile = $profileRepository->loadUserByUsername($username);
            if (!$profile) {
                throw new NotFoundHttpException();
            }
        } catch (\Exception $ex) {
            $this->logger->notice('controller.profile.view: user not found',
                ['username' => $username, 'message' => $ex->getMessage()]);
            throw new NotFoundHttpException($this->translator->trans('profile-not-found', ['username' => $username],
                'profile'), $ex);
        }

        return $this->render("Profile/view.html.twig");
    }

    /**
     * @Route("/profile/list", name="profile-list")
     */
    public function list()
    {

        return $this->render("Profile/list.html.twig");
    }

    /**
     * @Route("/profile/edit/{username}", name="profile-edit")
     */
    public function edit($username = null)
    {

    }


    /**
     * @Route("/profile/settings", name="profile-settings")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function settings(Request $request)
    {
        /** @var Profile $profile */
        $profile = $this->getUser();

        $form = $this->createForm(SettingsForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render('Profile/settings.html.twig', array('form' => $form->createView(), 'profile' => $profile));
    }

    /**
     * @Route("/profile/settings/enableTwoStepAuth", name="profile-settings-enable-two-step-auth")
     * @param GoogleAuthenticatorInterface $authService
     * @param SessionInterface $session
     * @param Request $request
     * @param ProfileService $profileService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function enableTwoStepAuth(
        GoogleAuthenticatorInterface $authService,
        SessionInterface $session,
        Request $request,
        ProfileService $profileService
    ) {
        /** @var Profile $profile */
        $profile = $this->getUser();
        $activationCode = $request->get('activation-code');

        // If activation code sent
        if ($activationCode) {
            $secret = $session->get('generated-secret');
            $profile->setGoogleAuthenticatorSecret($secret);

            // If activation code matches generated secret check
            if ($activationCode == $authService->checkCode($profile, $activationCode)) {
                $profileService->enableTwoStepAuth($profile, $secret);

                $this->addFlash('success', $this->translator->trans('two-step-auth-enabled', [], 'profile'));

                return $this->redirectToRoute('profile-settings');
            } else {
                $this->addFlash('danger', $this->translator->trans('two-step-auth-failed', [], 'profile'));
            }
        }

        $secret = $authService->generateSecret();
        $profile->setGoogleAuthenticatorSecret($secret);
        $session->set('generated-secret', $secret);

        $qrCode = $authService->getUrl($profile);

        return $this->render('Profile/two-step-auth.html.twig',
            array('qrCode' => $qrCode)
        );
    }

    /**
     * @Route("/profile/settings/disableTwoStepAuth", name="profile-settings-disable-two-step-auth")
     * @param ProfileService $profileService
     * @param UserInterface|Profile $profile
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disableTwoStepAuth(ProfileService $profileService, UserInterface $profile)
    {

        // TODO: Require user to either enter password again or add email token for this, security reasons
        $profileService->disableTwoStepAuth($profile);

        $this->addFlash('success', $this->translator->trans('two-step-auth-disabled', [], 'profile'));

        return $this->redirectToRoute('profile-settings');
    }

    /**
     * @Route("/profile/settings/forgetDevices", name="profile-settings-forget-devices")
     * @param ProfileService $profileService
     * @param UserInterface|Profile $user
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function forgetDevices(ProfileService $profileService, UserInterface $user)
    {
        $profileService->forgetDevices($user);
        $this->addFlash('success', $this->translator->trans('two-step-auth-devices-forgoten', [], 'profile'));

        return $this->redirectToRoute('profile-settings');
    }

    /**
     * @Route("/profile/settings/logoutEverywhere", name="profile-settings-logout-everywhere")
     */
    public function logoutEverywhere()
    {

    }

    /**
     * @Route("/profile/settings/delete-profile", name="profile-delete")
     */
    public function delete()
    {
        return $this->render('Profile/delete.html.twig');
    }


    /**
     * @Route("/profile/membership", name="profile-membership")
     */
    public function membership()
    {
        return $this->render("Profile/membership.html.twig");
    }
}