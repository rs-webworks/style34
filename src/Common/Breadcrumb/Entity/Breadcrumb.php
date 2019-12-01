<?php declare(strict_types=1);

namespace EryseClient\Common\Breadcrumb\Entity;

/**
 * Class Breadcrumb
 *
 * @package EryseClient\Common\Breadcrumb\Entity
 */
class Breadcrumb
{
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
}
