<?php

namespace CodeHuiter\Facilities\SearchList;

class SearchListResult
{
    /**
     * @var array
     */
    private $items;
    /**
     * @var int|null
     */
    private $itemsCount;
    /**
     * @var array
     */
    private $filters;
    /**
     * @var array
     */
    private $pages;

    public function __construct(array $items, array $filters, array $pages, ?int $itemsCount = null)
    {
        $this->items = $items;
        $this->filters = $filters;
        $this->pages = $pages;
        $this->itemsCount = $itemsCount;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getItemsCount(): ?int
    {
        return $this->itemsCount;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getPages(): array
    {
        return $this->pages;
    }
}