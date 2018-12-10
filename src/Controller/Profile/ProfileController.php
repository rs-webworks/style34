<?php

namespace Style34\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package Style34\Controller\Profile
 */
class ProfileController extends AbstractController {

	/**
	 * @Route("/profile/list", name="profile-list")
	 */
	public function list(){

		return $this->render("Profile/list.html.twig");
	}

    /**
     * @Route("/profile/logout", name="profile-logout")
     */
    public function logout()
    {
    }
}