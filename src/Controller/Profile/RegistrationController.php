<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController
 * @package App\Controller\Profile
 */
class RegistrationController extends AbstractController {

	/**
	 * @Route("/profile/registration", name="profile-registration")
	 */
	public function index(){

		return $this->render("Profile/Registration/index.html.twig");
	}

	/**
	 * @Route("/profile/registration/membership", name="profile-registration-membership")
	 */
	public function membership(){

		return $this->render("Profile/Registration/membership.html.twig");
	}
}