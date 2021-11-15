<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Task\Dependency\MentoringFilter;

class ShowAllMentoringPayload
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

    /**
     * 
     * @var MentoringFilter
     */
    protected $filter;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getFilter(): MentoringFilter
    {
        return $this->filter;
    }

    public function __construct(int $page, int $pageSize, MentoringFilter $filter)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->filter = $filter;
    }

}
