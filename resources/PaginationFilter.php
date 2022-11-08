<?php

namespace Resources;

class PaginationFilter
{

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function __construct(int $page = 1, int $pageSize = 25)
    {
        $this->page = $page ?: 1;
        $this->pageSize = $pageSize > 100 ? 100 : $pageSize;
    }

}
