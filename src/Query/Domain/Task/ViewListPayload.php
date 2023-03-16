<?php

namespace Query\Domain\Task;

class ViewListPayload
{

    public $result;

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
    protected $filter;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getFilter()
    {
        return $this->filter;
    }

    public function setPage(int $page)
    {
        $this->page = $page;
        return $this;
    }

    public function setPageSize(int $pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

}
