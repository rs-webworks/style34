<?php declare(strict_types=1);

namespace EryseClient\Controller\User;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Entity\Server\User\User;
use EryseClient\Form\User\SettingsForm;
use EryseClient\Repository\Server\User\ServerSettingsRepository;
use EryseClient\Repository\Server\User\UserRepository;
use EryseClient\Service\UserService;
use EryseClient\Utility\EntityManagersTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UserController
 * @package EryseClient\Controller\User
 * @IsGranted(EryseClient\Entity\Client\User\Role::INACTIVE)
 */
class UserController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagersTrait;

    /**
     * @Route("/user/edit/{id}/{username}", name="user-edit")
     * @param null $username
     */
    public function edit($username = null)
    {
    }

    /**
     * @Route("/user/list", name="user-list")
     */
    public function list()
    {
    }

    /**
     * @Route("/user", name="user-view-self")
     * @param $id
     * @param UserRepository $userRepository
     */
    public function viewSelf($id, UserRepository $userRepository)
    {
    }

    /**
     * @Route("/user/{id}/{username}", name="user-view", )
     * @param $id
     * @param UserRepository $userRepository
     */
    public function view($id, UserRepository $userRepository)
    {
    }

    /**
     * @Route("/user/settings", name="user-settings")
     * @param Request $request
     * @param ServerSettingsRepository $serverSettingsRepository
     * @return Response
     */
    public function settings(Request $request, ServerSettingsRepository $serverSettingsRepository)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(SettingsForm::class);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            //TODO: implement settings saving
//        }

        return $this->render(
            'User/settings.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
                'settings' => $serverSettingsRepository->findOneBy(["userId" => $user->getId()])
            ]
        );
    }

    /**
     * @Route("/user/settings/enable-two-step-auth", name="user-settings-enable-two-step-auth")
     * @param GoogleAuthenticatorInterface $authService
     * @param SessionInterface $session
     * @param Request $request
     * @param UserService $userService
     * @param UserRepository $userRepository
     * @param ServerSettingsRepository $serverSettingsRepository
     * @return Response
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function enableTwoStepAuth(
        GoogleAuthenticatorInterface $authService,
        SessionInterface $session,
        Request $request,
        UserService $userService,
        UserRepository $userRepository,
        ServerSettingsRepository $serverSettingsRepository
    ) {
        $user = $this->getUser();
        $gAuthEntity = $userRepository->getGoogleAuthEntity($user);
        $activationCode = $request->get('activation-code');
        $serverSettings = $serverSettingsRepository->findByUser($user);

        // If activation code sent
        if ($activationCode) {
            $secret = $session->get('generated-secret');
            $serverSettings->setGAuthSecret($secret);

            // If activation code matches generated secret check
            if ($activationCode == $authService->checkCode($gAuthEntity, $activationCode)) {
                $userService->enableTwoStepAuth($user, $secret);

                $this->addFlash('success', $this->translator->trans('two-step-auth-enabled', [], 'profile'));

                return $this->redirectToRoute('user-settings');
            } else {
                $this->addFlash('danger', $this->translator->trans('two-step-auth-failed', [], 'profile'));
            }
        }

        $secret = $authService->generateSecret();
        $serverSettings->setGAuthSecret($secret);
        $session->set('generated-secret', $secret);

        $qrCode = $authService->getUrl($gAuthEntity);

        return $this->render('User/two-step-auth.html.twig', ['qrCode' => $qrCode]);
    }

    /**
     * @Route("/user/settings/disableTwoStepAuth", name="user-settings-disable-two-step-auth")
     * @param UserService $userService
     * @param UserInterface|User $user
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function disableTwoStepAuth(UserService $userService, UserInterface $user)
    {
        // TODO: Require user to either enter password again or add email token for this, security reasons
        $userService->disableTwoStepAuth($user);

        $this->addFlash('success', $this->translator->trans('two-step-auth-disabled', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/user/settings/forgetDevices", name="user-settings-forget-devices")
     * @param UserService $userService
     * @param UserInterface|User $user
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function forgetDevices(UserService $userService, UserInterface $user)
    {
        $userService->forgetDevices($user);
        $this->addFlash('success', $this->translator->trans('two-step-auth-devices-forgotten', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/user/settings/logoutEverywhere", name="user-settings-logout-everywhere")
     * @param UserService $userService
     * @param UserInterface|User $user
     * @return RedirectResponse
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function logoutEverywhere(UserService $userService, UserInterface $user)
    {
        // TODO: This is not working yet
        $userService->logoutEverywhere($user);
        $this->addFlash('success', $this->translator->trans('settings-logged-out-everywhere', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/user/settings/delete-user", name="user-delete")
     */
    public function delete()
    {
        return $this->render('User/delete.html.twig');
    }
}
