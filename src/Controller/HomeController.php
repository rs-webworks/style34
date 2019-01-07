<?php

namespace EryseClient\Controller;

use EryseClient\Repository\Token\TokenTypeRepository;
use EryseClient\Service\ApplicationService;
use EryseClient\Traits\LoggerTrait;
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
     * @param TokenTypeRepository $tokenTypeRepository
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \EryseClient\Exception\Application\KeysAlreadyExists
     */
    public function dev(ApplicationService $applicationService, KernelInterface $kernel)
    {

        //TODO: rewrite this to: https://github.com/phpseclib/phpseclib
        //TODO: create custom rsa-key-service for such thing

        $token = '8997fb5c02b7292e39fc26fa4bc8b5a53b0f344c';
//        $applicationService->generateKeyPair($token, true);

        $privateKey = file_get_contents($kernel->getProjectDir() . '/config/rsa/private.key');
        $publicKey = file_get_contents($kernel->getProjectDir() . '/config/rsa/public.key');

        $prkRes = openssl_get_privatekey($privateKey, $token);
        $ppkRes = openssl_get_publickey($publicKey);

        dump($prkRes);
        dump($ppkRes);

        $encrypted = '';
        $decrypted = '';

        $plaintext = 'Lazy fox and shit';

        openssl_public_encrypt($plaintext, $encrypted, $ppkRes);
        openssl_public_decrypt($encrypted, $decrypted, $prkRes);

        dump($encrypted);
        dump($decrypted);

        return $this->render('dev.html.twig');
    }
}