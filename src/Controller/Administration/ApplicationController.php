<?php

namespace EryseClient\Controller\Administration;

use EryseClient\Utility\ApiClientTrait;
use EryseClient\Utility\EntityManagerTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use RaitoCZ\EryseServices\Exception\RsaService\InvalidKeyTypeException;
use RaitoCZ\EryseServices\Exception\RsaService\KeysAlreadyExistsException;
use RaitoCZ\EryseServices\Service\RsaService;
use RaitoCZ\EryseStructures\Routing\ServerRoutes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApplicationController
 * @package EryseClient\Controller\Administration\Application
 * IsGranted(EryseClient\Entity\User\Role::ADMIN)
 */
class ApplicationController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagerTrait;
    use ApiClientTrait;

    /**
     * @Route("/administration", name="administration-dashboard")
     * @return Response
     */
    public function dashboard()
    {

        return $this->render('Administration/dashboard.html.twig');
    }

    /**
     * @Route("/administration/server-configuration", name="administration-server-configuration")
     * @param RsaService $rsaService
     * @return Response
     * @throws GuzzleException
     * @throws InvalidKeyTypeException
     */
    public function serverConfiguration(RsaService $rsaService)
    {
        $rsaKeys = $rsaService->checkForKeys();
        $publicKey = $rsaKeys ? $rsaService->loadPublicKey() : null;

        try {
            $serverPing = $this->apiClient->call(ServerRoutes::API_SERVER_PING);
        }catch(ConnectException $exception){
            $serverPing = new \GuzzleHttp\Psr7\Response(Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return $this->render('Administration/server-configuration.html.twig', array(
            'rsaKeys' => $rsaKeys,
            'publicKey' => $publicKey,
            'serverPing' => $serverPing
        ));
    }

    /**
     * @Route("/administration/server-configuration/generate-keys", name="administration-server-configuration-generate-keys")
     * @param RsaService $rsaService
     * @return Response
     * @throws \RaitoCZ\EryseServices\Exception\RsaService\KeysAlreadyExistsException
     */
    public function generateRsaKeys(RsaService $rsaService)
    {
        try {
            $rsaService->generateKeyPair($this->getParameter('eryseClient')['server']['token']);
            $this->addFlash('success', $this->translator->trans('rsa-keys-generated', [], 'administration'));
        } catch (KeysAlreadyExistsException $ex) {
            $this->addFlash('danger', $this->translator->trans('rsa-keys-already-generated', [], 'administration'));
        }

        return $this->redirectToRoute('administration-server-configuration');
    }

}