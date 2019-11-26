<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Client\Controller;

use EryseClient\Common\Utility\TranslatorAwareTrait;
use EryseClient\Common\Utility\LoggerAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApplicationController
 *
 * @package EryseClient\Controller\Administration\Application
 * @IsGranted(EryseClient\Server\UserRole\Entity\UserRole::ADMIN)
 */
class ClientController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @Route("/administration", name="administration-dashboard")
     * @return Response
     */
    public function dashboard()
    {

        return $this->render('Administration/dashboard.html.twig');
    }
}
