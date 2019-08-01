<?php declare(strict_types=1);
namespace EryseClient\Controller;

use EryseClient\Utility\LoggerTrait;
use phpseclib\Crypt\RSA;
use RaitoCZ\EryseServices\Service\RsaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package EryseClient\Controller
 */
class HomeController extends AbstractController
{
    use LoggerTrait;

    /**
     * @Route("/", name="home-index")
     */
    public function index()
    {
        return $this->render('Home/index.html.twig');
    }

    /**
     * @Route("/dev", name="dev")
     */
    public function dev()
    {


        return $this->render('dev.html.twig');
    }
}