<?php declare(strict_types=1);

namespace EryseClient\Common\Breadcrumb\Entity;

/**
 * Class Breadcrumb
 *
 * @package EryseClient\Common\Breadcrumb\Entity
 */
class BreadcrumbItem
{
    /** @var string */
    protected $route;

    /** @var string */
    protected $name;

    /**
     * BreadcrumbItem constructor.
     *
     * @param string $route
     * @param string $name
     */
    public function __construct(string $route, string $name)
    {
        $this->route = $route;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
