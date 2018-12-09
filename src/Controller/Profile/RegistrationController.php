<?php

namespace Style34\Controller\Profile;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Style34\Entity\Profile\Profile;
use Style34\Entity\Token\Token;
use Style34\Form\Profile\RegistrationForm;
use Style34\Service\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RegistrationController
 * @package Style34\Controller\Profile
 */
class RegistrationController extends AbstractController
{
    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var LoggerInterface $logger */
    protected $logger;

    public function __construct(TranslatorInterface $translator, LoggerInterface $logger)
    {
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * @Route("/profile/registration", name="profile-registration")
     * @param Request $request
     * @param ProfileService $profileService
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Throwable
     */
    public function index(Request $request, ProfileService $profileService)
    {
        $profileService->purgeExpiredRegistrations();

        $profile = new Profile();
        $form = $this->createForm(RegistrationForm::class, $profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $profileService->registerNewProfile($profile);

                $this->addFlash('success', $this->translator->trans('registration-success', [], 'profile'));

                return $this->redirectToRoute("profile-registration-success");
            } catch (\Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('registration-failed', [], 'profile'));
                $this->logger->error('profile.registration-failed', array($ex, $profile));
            }
        }

        return $this->render(
            'Profile/Registration/index.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/profile/registration/activate/{tokenHash}", name="profile-registration-activate")
     * @param EntityManagerInterface $em
     * @param ProfileService $profileService
     * @param $tokenHash
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function activate(EntityManagerInterface $em, ProfileService $profileService, $tokenHash)
    {
        try {
            $tokenRepository = $em->getRepository(Token::class);

            /** @var Token $token */
            if ($token = $tokenRepository->findOneBy(array('hash' => $tokenHash))) {
                $profileService->activateProfile($token->getProfile(), $token);
            }

            $this->addFlash('success', $this->translator->trans('activation-success', [], 'profile'));
        } catch (\Exception $ex) {
            $this->addFlash(
                'danger',
                $this->translator->trans('activation-failed', [], 'profile') . ' - ' . $ex->getMessage()
            );
            $this->logger->error('profile.activation-failed', array($ex, $tokenHash));
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