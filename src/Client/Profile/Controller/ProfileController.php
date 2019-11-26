<?php declare(strict_types=1);

namespace EryseClient\Client\Profile\Controller;

use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Common\Utility\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 *
 * @package EryseClient\Controller\Profile
 */
class ProfileController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

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
