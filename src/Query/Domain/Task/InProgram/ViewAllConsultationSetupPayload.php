<?php

namespace Query\Domain\Task\InProgram;

class ViewAllConsultationSetupPayload
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

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

}
