<?php declare(strict_types=1);

namespace EryseClient\Controller\Administration;

use EryseClient\Utility\EntityManagersTrait;
use EryseClient\Utility\LoggerTrait;
use EryseClient\Utility\TranslatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApplicationController
 * @package EryseClient\Controller\Administration\Application
 * @IsGranted(EryseClient\Entity\Client\Profile\Role::ADMIN)
 */
class ApplicationController extends AbstractController
{
    use TranslatorTrait;
    use LoggerTrait;
    use EntityManagersTrait;

    /**
     * @Route("/administration", name="administration-dashboard")
     * @return Response
     */
    public function dashboard()
    {

        return $this->render('Administration/dashboard.html.twig');
    }
}
