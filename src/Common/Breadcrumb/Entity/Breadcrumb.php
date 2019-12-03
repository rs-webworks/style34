<?php declare(strict_types=1);

namespace EryseClient\Common\Breadcrumb\Entity;

/**
 * Class Breadcrumb
 *
 * @package EryseClient\Common\Breadcrumb\Entity
 */
class Breadcrumb
{
    public const ROUTE = "route";
    public const NAME = "name";

    /** @var BreadcrumbItem[] */
    protected $items;

    /**
     * @param BreadcrumbItem $breadcrumbItem
     *
     * @return void
     */
    public function addItem(BreadcrumbItem $breadcrumbItem): void
    {
        $this->items[] = $breadcrumbItem;
    }

    /**
     * @return BreadcrumbItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param BreadcrumbItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function createFromArray(array $array): self
    {
        foreach ($array as $item) {
            $this->addItem(new BreadcrumbItem($item[self::NAME], $item[self::ROUTE]));
        }

        return $this;
    }

    /**
     * @param string $route
     * @param string $name
     *
     * @return Breadcrumb
     */
    public function addNewItem(string $name, ?string $route = null): self
    {
        $this->addItem(new BreadcrumbItem($name, $route));
        return $this;
    }
}
