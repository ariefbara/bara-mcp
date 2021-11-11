<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot\BookedMentoringSlotFilter;

class ViewAllBookedMentoringSlotsPayload
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
     * @var BookedMentoringSlotFilter|null
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

    public function getFilter(): ?BookedMentoringSlotFilter
    {
        return $this->filter;
    }

    public function __construct(?int $page, ?int $pageSize, ?BookedMentoringSlotFilter $filter)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->filter = $filter;
    }

}
