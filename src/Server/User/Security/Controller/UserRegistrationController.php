<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Controller;

use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\Token\Repository\TokenRepository;
use EryseClient\Server\User\Event\AfterRegistrationEvent;
use EryseClient\Server\User\Event\BeforeRegistrationEvent;
use EryseClient\Server\User\Exception\ActivationException;
use EryseClient\Server\User\Facade\RegisterUserFacade;
use EryseClient\Server\User\Form\Type\RegistrationType;
use EryseClient\Server\User\Repository\UserRepository;
use EryseClient\Server\User\Service\UserService;
use EryseClient\Server\User\Validator\UserValidator;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserRegistrationController
 * @Route("/user/registration")
 */
class UserRegistrationController extends AbstractController
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @Route(name="user-registration")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param RegisterUserFacade $registerUserFacade
     * @param EventDispatcherInterface $dispatcher
     *
     * @return RedirectResponse|Response
     */
    public function registration(
        Request $request,
        UserRepository $userRepository,
        RegisterUserFacade $registerUserFacade,
        EventDispatcherInterface $dispatcher
    ) : Response {
        $dispatcher->dispatch(new BeforeRegistrationEvent());

        $userValidator = new UserValidator();
        $form = $this->createForm(RegistrationType::class, $userValidator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userEntity = $registerUserFacade->createFromValidator($userValidator, $request->getClientIp());
                $userRepository->saveAndCreateSettings($userEntity);

                $dispatcher->dispatch(new AfterRegistrationEvent($userEntity));

                // Flash & redirect
                $this->addFlash('success', $this->translator->trans('registration-success', [], 'profile'));

                return $this->redirectToRoute('user-registration-success');
            } catch (Exception $ex) {
                $this->addFlash('danger', $this->translator->trans('registration-failed', [], 'profile'));
                $this->logger->error(
                    'controller.user.security.registration: registration failed',
                    [$ex, $userValidator]
                );
            }
        }

        return $this->render(
            'User/Security/registration.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/activate/{tokenHash}", name="user-registration-activate")
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
    ) : Response {
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
     * @Route("/success", name="user-registration-success")
     * @return Response
     */
    public function success() : Response
    {
        return $this->render('User/Security/registration-success.html.twig');
    }
}
