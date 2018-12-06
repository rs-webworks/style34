<?php

namespace Style34\Controller\Profile;

use Psr\Log\LoggerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Form\Profile\RegistrationForm;
use Style34\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RegistrationController
 * @package Style34\Controller\Profile
 */
class RegistrationController extends AbstractController
{

    /**
     * @Route("/profile/registration", name="profile-registration")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param ProfileService $profileService
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(
        Request $request,
        TranslatorInterface $translator,
        ProfileService $profileService,
        LoggerInterface $logger
    ) {
        $profile = new Profile();
        $form = $this->createForm(RegistrationForm::class, $profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $profileService->registerNewProfile($profile);
                $this->addFlash('success', $translator->trans('registration-success', [], 'profile'));
            } catch (\Exception $ex) {
                $this->addFlash('error', $translator->trans('registration-failed', [], 'profile'));
                $logger->error('profile.failed-registration', array($ex, $profile));
            }
        }

        return $this->render(
            'Profile/Registration/index.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/profile/registration/membership", name="profile-registration-membership")
     */
    public function membership()
    {

        return $this->render("Profile/Registration/membership.html.twig");
    }
}