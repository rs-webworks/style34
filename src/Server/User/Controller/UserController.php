<?php declare(strict_types=1);

namespace EryseClient\Server\User\Controller;

use Doctrine\ORM\EntityNotFoundException;
use EryseClient\Client\Profile\Settings\Form\Type\SettingsType;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Exception\UserException;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use EryseClient\Server\User\Settings\Repository\SettingsRepository;
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
 *
 * @IsGranted(EryseClient\Server\User\Role\Entity\RoleEntity::INACTIVE)
 * @Route("/user")
 */
class UserController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @Route("", name="user-view-self")
     * @return Response
     */
    public function viewSelf() : Response
    {
        return $this->render('User/view.html.twig');
    }

    /**
     * @Route("/edit/{id}/{username}", name="user-edit")
     * @param null $username
     *
     * @return Response
     */
    public function edit($username = null) : Response
    {
        return $this->render('User/edit.html.twig');
    }

    /**
     * @Route("/settings", name="user-settings")
     * @param Request $request
     * @param SettingsRepository $serverSettingsRepository
     *
     * @return Response
     */
    public function settings(Request $request, SettingsRepository $serverSettingsRepository) : Response
    {
        /** @var UserEntity $user */
        $user = $this->getUser();

        $form = $this->createForm(SettingsType::class);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            //TODO: implement settings saving
//        }

        return $this->render(
            'User/settings.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
                'settings' => $serverSettingsRepository->findOneBy(['userId' => $user->getId()])
            ]
        );
    }

    /**
     * @Route("/settings/enable-two-step-auth", name="user-settings-enable-two-step-auth")
     * @param GoogleAuthenticatorInterface $authService
     * @param SessionInterface $session
     * @param Request $request
     * @param UserService $userService
     * @param UserRepository $userRepository
     * @param SettingsRepository $serverSettingsRepository
     *
     * @return Response
     * @throws UserException
     * @throws EntityNotFoundException
     */
    public function enableTwoStepAuth(
        GoogleAuthenticatorInterface $authService,
        SessionInterface $session,
        Request $request,
        UserService $userService,
        UserRepository $userRepository,
        SettingsRepository $serverSettingsRepository
    ) : Response {
        $user = $this->getUser();
        $gAuthEntity = $userRepository->getGoogleAuthEntity($user);
        $activationCode = $request->get('activation-code');
        $serverSettings = $serverSettingsRepository->findByUser($user);

        // If activation code sent
        if ($activationCode) {
            $secret = $session->get('generated-secret');
            $serverSettings->setGAuthSecret($secret);

            // If activation code matches generated secret check
            if ($activationCode === $authService->checkCode($gAuthEntity, $activationCode)) {
                $userService->enableTwoStepAuth($user, $secret);

                $this->addFlash('success', $this->translator->trans('two-step-auth-enabled', [], 'profile'));

                return $this->redirectToRoute('user-settings');
            }

            $this->addFlash('danger', $this->translator->trans('two-step-auth-failed', [], 'profile'));
        }

        $secret = $authService->generateSecret();
        $serverSettings->setGAuthSecret($secret);
        $session->set('generated-secret', $secret);

        $qrCode = $authService->getUrl($gAuthEntity);

        return $this->render('User/two-step-auth.html.twig', ['qrCode' => $qrCode]);
    }

    /**
     * @Route("/settings/disableTwoStepAuth", name="user-settings-disable-two-step-auth")
     * @param UserService $userService
     * @param UserInterface|UserEntity $user
     *
     * @return RedirectResponse
     */
    public function disableTwoStepAuth(UserService $userService, UserInterface $user) : RedirectResponse
    {
        // TODO: Require user to either enter password again or add email token for this, security reasons
        $userService->disableTwoStepAuth($user);

        $this->addFlash('success', $this->translator->trans('two-step-auth-disabled', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/settings/forgetDevices", name="user-settings-forget-devices")
     * @param UserService $userService
     * @param UserInterface|UserEntity $user
     *
     * @return RedirectResponse
     */
    public function forgetDevices(UserService $userService, UserInterface $user) : RedirectResponse
    {
        $userService->forgetDevices($user);
        $this->addFlash('success', $this->translator->trans('two-step-auth-devices-forgotten', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/settings/logoutEverywhere", name="user-settings-logout-everywhere")
     * @param UserService $userService
     * @param UserInterface|UserEntity $user
     *
     * @return RedirectResponse
     */
    public function logoutEverywhere(UserService $userService, UserInterface $user) : RedirectResponse
    {
        // TODO: This is not working yet
        $userService->logoutEverywhere($user);
        $this->addFlash('success', $this->translator->trans('settings-logged-out-everywhere', [], 'user'));

        return $this->redirectToRoute('user-settings');
    }

    /**
     * @Route("/settings/delete-user", name="user-delete")
     */
    public function delete() : Response
    {
        return $this->render('User/delete.html.twig');
    }
}
