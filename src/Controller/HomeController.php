<?php

namespace Style34\Controller;

use BrowscapPHP\Browscap;
use BrowscapPHP\BrowscapUpdater;
use Psr\SimpleCache\CacheInterface;
use Style34\Repository\Token\TokenTypeRepository;
use Style34\Traits\LoggerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     */
    public function dev(Request $request, CacheInterface $cache)
    {

        $browscap_updater = new BrowscapUpdater($cache, $this->logger);
        $browscap_updater->update(\BrowscapPHP\Helper\IniLoader::PHP_INI_FULL);

        $bc = new Browscap($cache, $this->logger);
        dump($bc);




        $ua = $request->server->get('HTTP_USER_AGENT');
        dump($bc->getBrowser($ua));
        return $this->render('dev.html.twig');
    }
}