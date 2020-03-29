<?php declare(strict_types=1);

namespace EryseClient\Client\Administration\Dashboard\Controller;

use EryseClient\Client\Administration\Controller\AbstractAdminController;
use EryseClient\Client\Administration\Dashboard\Voter\DashboardVoter;
use EryseClient\Common\Utility\LoggerAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DashboardController
 * @Route("/administration/dashboard")
 */
class DashboardController extends AbstractAdminController
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    /**
     * @Route("", name="administration-dashboard")
     * @return Response
     */
    public function dashboard() : Response
    {
        $this->denyAccessUnlessGranted(DashboardVoter::VIEW, DashboardVoter::DASHBOARD);
        return $this->render('Administration/Dashboard/dashboard.html.twig');
    }
}
