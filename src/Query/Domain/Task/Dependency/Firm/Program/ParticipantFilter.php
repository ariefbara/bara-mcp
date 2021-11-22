<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

class ParticipantFilter
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
     * @var bool|null
     */
    protected $activeStatus;

    /**
     * 
     * @var array|null
     */
    protected $programIdList;

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

    public function getProgramIdList(): ?array
    {
        return $this->programIdList;
    }

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function setActiveStatus(?bool $activeStatus)
    {
        $this->activeStatus = $activeStatus;
        return $this;
    }

    public function setProgramIdList(array $programIdList)
    {
        $this->programIdList = $programIdList;
        return $this;
    }

}
