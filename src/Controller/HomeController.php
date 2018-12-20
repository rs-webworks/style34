<?php

namespace Style34\Controller;

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;
use Style34\Entity\Token\TokenType;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Service\CryptService;
use Style34\Service\GoogleAuthService;
use Style34\Traits\LoggerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package Style34\Controller
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
     * @param TokenTypeRepository $tokenTypeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function dev(TokenTypeRepository $tokenTypeRepository, GoogleAuthService $googleAuthService)
    {
        $this->logger->notice('controller.home.dev: hola');
        return $this->render('dev.html.twig', array('qr' => $qr));

    }
}