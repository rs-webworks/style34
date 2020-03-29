<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Controller;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use EryseClient\Common\Service\MailService;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\Token\Service\TokenService;
use EryseClient\Server\User\Entity\UserEntity;
use EryseClient\Server\User\Exception\ActivationException;
use EryseClient\Server\User\Form\Type\RegistrationType;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserRegistrationController
 *
 *
 */
class UserRegistrationController extends AbstractController
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @Route("/user/registration", name="user-registration")
     * @param Request $request
     * @param UserService $userService
     * @param MailService $mailService
     * @param TokenService $tokenService
     * @param TokenRepository $tokenRepository
     * @param UserRepository $userRepository
     *
     * @return RedirectResponse|Response
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     */
    public function registration(
        Request $request,
        UserService $userService,
        MailService $mailService,
        TokenService $tokenService,
        TokenRepository $tokenRepository,
        UserRepository $userRepository
    ): Response {
        // Purge all expired & invalid requests for registration
        $expiredRegistrations = $userService->getExpiredRegistrations();
        if ($expiredRegistrations) {
            $userRepository->removeUsers($expiredRegistrations);
        }

        // Load form data
        $user = new UserEntity();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Prepare user & token
                $thisIp = $request->getClientIp();
                $user = $userService->prepareNewUser($user, $thisIp);
                $userRepository->saveNew($user);

                $token = $tokenService->getActivationToken($user);

                // Send registration email
                $mailService->sendActivationMail($user, $token);
                $tokenRepository->save($token);

                // Flash & redirect
                $this->addFlash('success', $this->translator->trans('registration-success', [], 'profile'));

                return $this->redirectToRoute('user-registration-success');
            } catch (Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('registration-failed', [], 'profile'));
                $this->logger->error(
                    'controller.user.security.registration: registration failed',
                    [$ex, $user]
                );
            }
        }

        return $this->render(
            'User/Security/registration.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/user/registration/activate/{tokenHash}", name="user-registration-activate")
     * @param UserService $userService
     * @param UserRepository $userRepository
     * @param TokenRepository $tokenRepository
     * @param $tokenHash
     *
     * @return Response
     */
    public function activate(
        UserService $userService,
        UserRepository $userRepository,
        TokenRepository $tokenRepository,
        $tokenHash
    ): Response {
        try {
            $token = $tokenRepository->findOneBy(['hash' => $tokenHash]);

            if (!$token) {
                throw new ActivationException($this->translator->trans('activation-invalid-token', [], 'profile'));
            }

            $user = $userRepository->find($token->getUser()->getId());
            $user = $userService->activateUser($user, $token);
            $token->setInvalid(true);

            $userRepository->save($user);
            $tokenRepository->save($token);

            $this->addFlash('success', $this->translator->trans('activation-success', [], 'profile'));
        } catch (Exception $ex) {
            $this->addFlash(
                'danger',
                $this->translator->trans('activation-failed', [], 'profile') . ' - ' . $ex->getMessage()
            );
            $this->logger->error('controller.user.security.activate: activation failed', [$ex, $tokenHash]);
        }

        return $this->render('User/Security/activation.html.twig');
    }

    /**
     * @Route("/user/registration/success", name="user-registration-success")
     * @return Response
     */
    public function success(): Response
    {
        return $this->render('User/Security/success.html.twig');
    }
}
