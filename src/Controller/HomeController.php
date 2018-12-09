<?php
namespace Style34\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Style34\Entity\Token\Token;
use Style34\Entity\Token\TokenType;
use Style34\Service\TokenService;
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

    /**
     * @Route("/dev", name="dev")
     */
	public function dev(EntityManagerInterface $em){

	    $tokenType = $em->getRepository(TokenType::class)->findOneBy(array('name' => TokenType::PROFILE['ACTIVATION']));
	    $tokens = $em->getRepository(Token::class)->findExpiredTokens($tokenType);

	    dump($tokens);


        return $this->render('dev.html.twig');
    }
}