<?php declare(strict_types=1);

namespace EryseClient\Controller\Profile;

use EryseClient\Repository\Client\Profile\ProfileRepository;
use EryseClient\Utility\EntityManagersTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package EryseClient\Controller\Profile
 */
class ProfileController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagersTrait;

    /**
     * @Route("/profile/view/{username}", name="profile-view")
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