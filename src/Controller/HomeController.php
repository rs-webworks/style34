<?php

namespace EryseClient\Controller;

use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Service\RsaService;
use EryseClient\Traits\LoggerTrait;
use phpseclib\Crypt\RSA;
use Psr\SimpleCache\CacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param RsaService $rsaService
     * @param KernelInterface $kernel
     * @param RSA $crypt
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EryseClient\Exception\Security\InvalidKeyTypeException
     * @throws \EryseClient\Exception\Security\KeysAlreadyExistsException
     */
    public function dev(RsaService $rsaService, KernelInterface $kernel)
    {

        $token = '8997fb5c02b7292e39fc26fa4bc8b5a53b0f344c';
        $rsaService->generateKeyPair($token, true);

        $plaintext = 'Lazy fox and shit';

        $crypt = $rsaService->rsaEncodeMessage($plaintext, RsaService::PRIVATE_KEY_FILE);
        dump($crypt);
        $decrypt = $rsaService->rsaDecodeMessage($crypt, RsaService::PUBLIC_KEY_FILE);
        dump($decrypt);



        return $this->render('dev.html.twig');
    }
}