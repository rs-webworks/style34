<?php declare(strict_types=1);

namespace EryseClient\Common\Controller;

use EryseClient\Common\Breadcrumb\Entity\Breadcrumb;
use EryseClient\Common\Breadcrumb\Entity\BreadcrumbItem;
use EryseClient\Common\Utility\EryseAppAwareTrait;
use EryseClient\Common\Utility\TranslatorAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractController
 *
 *
 */
class AbstractController extends SymfonyAbstractController implements ControllerSettings
{
    use TranslatorAwareTrait;
    use EryseAppAwareTrait;

    public const BREADCRUMB_CONTROLLER = [];

    /**
     * @param Request $request
     *
     * @return int|null Default is 1 if no param is supplied
     */
    protected function getPageParam(Request $request): ?int
    {
        return $request->query->get("page") ? (int) $request->query->get("page") : 1;
    }

    /**
     * @return Breadcrumb
     */
    public function getControllerBreadcrumb(): Breadcrumb
    {
        $breadcrumb = new Breadcrumb();

        $breadcrumb->addItem(
            new BreadcrumbItem($this->clientApp->getName())
        );

        return $breadcrumb;
    }

    /**
     * @return Breadcrumb
     */
    public function getAdminControllerBreadcrumb(): Breadcrumb
    {
        $breadcrumb = $this->getControllerBreadcrumb();

        $breadcrumb->addItem(
            new BreadcrumbItem($this->translator->trans("dashboard", [], "administration"), "administration-dashboard")
        );

        return $breadcrumb;
    }
}
