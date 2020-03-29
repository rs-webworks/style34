<?php declare(strict_types=1);

namespace EryseClient\Server\User\Security\Controller;

use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Server\User\Security\Authenticator\UserLoginFormAuthenticator;
use EryseClient\Server\User\Security\Form\Type\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 *
 *
 */
class UserSecurityController extends AbstractController
{
    use LoggerAwareTrait;
    use TranslatorAwareTrait;

    /**
     * @Route("/user-security-login", name="user-security-login")
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $loginForm = $this->createForm(
            LoginType::class,
            null,
            ['action' => $this->generateUrl(UserLoginFormAuthenticator::ROUTE)]
        );

        return $this->render(
            'User/Login/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'loginForm' => $loginForm->createView(),
            ]
        );
    }

    /**
     * @return Response
     */
    public function navbarLoginForm(): Response
    {
        $loginForm = $this->createForm(
            LoginType::class,
            null,
            ['attr' => ['id' => 'login'], 'action' => $this->generateUrl(UserLoginFormAuthenticator::ROUTE)]
        );

        return $this->render('_partial/login-form.html.twig', ['loginForm' => $loginForm->createView()]);
    }

    /**
     * @Route("/logout", name="user-security-logout")
     */
    public function logout(): void
    {
    }
}
