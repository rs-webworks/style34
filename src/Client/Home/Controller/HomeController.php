<?php declare(strict_types=1);

namespace EryseClient\Client\Home\Controller;

use EryseClient\Common\Utility\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 *
 */
class HomeController extends AbstractController
{
    use LoggerAwareTrait;

    /**
     * @Route("/", name="home-index")
     */
    public function index()
    {
        return $this->render('Home/index.html.twig');
    }

    /**
     * @Route("/dev", name="dev")
     * @return Response
     */
    public function dev()
    {
        return $this->render('dev.html.twig');
    }
}
