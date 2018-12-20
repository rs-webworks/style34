<?php

namespace Style34\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Profile\Profile;
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
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package Style34\Controller\Profile
 */
class RegistrationController extends AbstractController
{
    use EntityManagerTrait;
    use LoggerTrait;
    use TranslatorTrait;

    /**
     * @Route("/profile/registration", name="profile-registration")
     * @param Request $request
     * @param ProfileService $profileService
     * @param MailService $mailService
     * @param TokenService $tokenService
     * @param ProfileRepository $profileRepository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(
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
            'Profile/Registration/index.html.twig',
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

        return $this->render('Profile/Registration/activation.html.twig');
    }

    /**
     * @Route("/profile/registration/success", name="profile-registration-success")
     */
    public function success()
    {
        return $this->render("Profile/Registration/success.html.twig");
    }


    /**
     * @Route("/profile/registration/membership", name="profile-registration-membership")
     */
    public function membership()
    {
        return $this->render("Profile/Registration/membership.html.twig");
    }
}