<?php

namespace App\Model;

class CategoryListResponse
{
    /**
     * @var CategoryListItem[]
     */
    private array $items;

    /**
     * @param CategoryListItem[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @return CategoryListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
