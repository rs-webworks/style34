<?php

namespace EryseClient\Controller\Administration;

use RaitoCZ\EryseServices\Service\RsaService;
use EryseClient\Utility\EntityManagerTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
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
     * @throws \RaitoCZ\EryseServices\Exception\RsaService\InvalidKeyTypeException
     */
    public function serverConfiguration(RsaService $rsaService)
    {
        $rsaKeys = $rsaService->checkForKeys();
        $publicKey = $rsaKeys ? $rsaService->loadPublicKey() : null;

        return $this->render('Administration/server-configuration.html.twig', array(
            'rsaKeys' => $rsaKeys,
            'publicKey' => $publicKey
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
        $rsaService->generateKeyPair($this->getParameter('eryseClient')['server']['token']);
        $this->addFlash('success', $this->translator->trans('rsa-keys-generated', [], 'administration'));


        return $this->redirectToRoute('administration-server-configuration');
    }

}