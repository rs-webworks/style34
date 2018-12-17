<?php

namespace Style34\Controller;

use Style34\Entity\Token\TokenType;
use Style34\Repository\Token\TokenTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package Style34\Controller
 */
class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home-index")
     */
    public function index()
    {
        return $this->render('Home/index.html.twig');
    }

    /**
     * @Route("/dev", name="dev")
     * @param TokenTypeRepository $tokenTypeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dev(TokenTypeRepository $tokenTypeRepository)
    {
        $tt = $tokenTypeRepository->findOneBy(array('name' => TokenType::PROFILE['ACTIVATION']));
        dump($tt);
        return $this->render('dev.html.twig');
    }
}