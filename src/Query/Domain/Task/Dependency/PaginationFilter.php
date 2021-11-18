<?php

namespace Query\Domain\Task\Dependency;

class PaginationFilter
{

    /**
     * 
     * @var int|null
     */
    protected $page;

    /**
     * 
     * @var int|null
     */
    protected $pageSize;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function __construct(?int $page, ?int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

}
