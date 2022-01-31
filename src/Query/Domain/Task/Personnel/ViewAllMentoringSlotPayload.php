<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotFilter;

class ViewAllMentoringSlotPayload
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

    /**
     * 
     * @var MentoringSlotFilter
     */
    protected $filter;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getFilter(): MentoringSlotFilter
    {
        return $this->filter;
    }

    public function __construct(?int $page, ?int $pageSize, MentoringSlotFilter $filter)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->filter = $filter;
    }

}
