<?php

namespace EryseClient\Controller;

use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Service\ApplicationService;
use EryseClient\Traits\LoggerTrait;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param TokenTypeRepository $tokenTypeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EryseClient\Exception\Application\KeysAlreadyExists
     */
    public function dev(ApplicationService $applicationService)
    {

        return $this->render('dev.html.twig');
    }
}