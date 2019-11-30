<?php declare(strict_types=1);

namespace EryseClient\Common\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 *
 * @package EryseClient\Common\Controller
 */
class AbstractController extends SymfonyAbstractController implements ControllerSettings
{

    /**
     * @param Request $request
     *
     * @return int|null Default is 1 if no param is supplied
     */
    protected function getPageParam(Request $request): ?int
    {
        return $request->query->get("page") ? (int) $request->query->get("page") : 1;
    }
}
