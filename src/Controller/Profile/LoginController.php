<?php

namespace Style34\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class LoginController
 * @package Style34\Controller\Profile
 */
class LoginController extends AbstractController {

	/**
	 * @Route("/login", name="profile-login")
	 */
	public function login(AuthenticationUtils $authenticationUtils){
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
}