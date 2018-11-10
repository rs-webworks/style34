<?php

namespace App\Controller\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller\Profile
 */
class ProfileController extends AbstractController {

	/**
	 * @Route("/profile/list", name="profile-list")
	 */
	public function list(){

		return $this->render("Profile/list.html.twig");
	}
}