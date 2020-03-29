<?php declare(strict_types=1);

namespace EryseClient\Common\Breadcrumb\Extension;

use EryseClient\Common\Breadcrumb\Entity\Breadcrumb as BreadcrumbEntity;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class Breadcrumb
 */
class Breadcrumb extends AbstractExtension
{

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'renderBreadcrumbs',
                [$this, 'renderBreadcrumbs'],
                [
                    'needs_environment' => true,
                 'is_safe' => ['html']]
            )
        ];
    }

    /**
     * @param BreadcrumbEntity $breadcrumb
     * @param Environment $environment
     *
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderBreadcrumbs(Environment $environment, BreadcrumbEntity $breadcrumb): string
    {
        dump($breadcrumb);
        return $environment->render('_extension/Breadcrumb.html.twig', ['breadcrumbs' => $breadcrumb]);
    }
}
