<?php

namespace Query\Domain\Task\InProgram;

class ViewAllSponsorsPayload
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
     * @var ?bool
     */
    protected $activeStatus;

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getActiveStatus(): ?bool
    {
        return $this->activeStatus;
    }

    public function __construct(int $page, int $pageSize, ?bool $activeStatus)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->activeStatus = $activeStatus;
    }

}
