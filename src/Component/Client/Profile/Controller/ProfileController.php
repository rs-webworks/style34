<?php declare(strict_types=1);

namespace EryseClient\Component\Client\Profile\Controller;

use EryseClient\Component\Common\Utility\LoggerTrait;
use EryseClient\Component\Common\Utility\TranslatorTrait;
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

    /**
     * @Route("/profile/view/{username}", name="profile-view")
     * @return Response
     */
    public function view()
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
