<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Dashboard\Controller;

use EryseClient\Client\Administration\Dashboard\Voter\DashboardVoter;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 */
class DashboardController extends AbstractController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @Route("/administration", name="administration-dashboard")
     * @return Response
     */
    public function dashboard(): Response
    {
        $this->denyAccessUnlessGranted(DashboardVoter::VIEW, DashboardVoter::DASHBOARD);
        return $this->render('Administration/Dashboard/dashboard.html.twig');
    }
}
