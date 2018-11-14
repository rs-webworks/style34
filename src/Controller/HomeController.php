<?php
namespace Style34\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package Style34\Controller
 */
class HomeController extends AbstractController {

	/**
	 * @Route("/", name="home-index")
	 */
	public function index(){
		return $this->render('Home/index.html.twig');
	}
}