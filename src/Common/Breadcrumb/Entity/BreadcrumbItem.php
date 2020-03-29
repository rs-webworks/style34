<?php declare(strict_types=1);

namespace EryseClient\Common\Breadcrumb\Entity;

/**
 * Class Breadcrumb
 *
 *
 */
class BreadcrumbItem
{
    /** @var string */
    protected ?string $route;

    /** @var string */
    protected string $name;

    /**
     * BreadcrumbItem constructor.
     *
     * @param string $name
     * @param string|null $route
     */
    public function __construct(string $name, ?string $route = null)
    {
        $this->route = $route;
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string|null $route
     */
    public function setRoute(?string $route): void
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
