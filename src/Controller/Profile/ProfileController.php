<?php

namespace EryseClient\Controller\Profile;

use EryseClient\Repository\Profile\ProfileRepository;
use EryseClient\Utility\EntityManagerTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package EryseClient\Controller\Profile
 */
class ProfileController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagerTrait;

    /**
     * @Route("/profile/view/{username}", name="profile-view")
     * @param ProfileRepository $profileRepository
     * @param null $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view(ProfileRepository $profileRepository, $username = null)
    {

        return $this->render("Profile/view.html.twig");
    }

    /**
     * @Route("/profile/list", name="profile-list")
     */
    public function list()
    {

        return $this->render("Profile/list.html.twig");
    }


    /**
     * @Route("/profile/membership", name="profile-membership")
     */
    public function membership()
    {
        return $this->render("Profile/membership.html.twig");
    }

}