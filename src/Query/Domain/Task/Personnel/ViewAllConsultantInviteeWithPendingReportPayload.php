<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantInviteeFilter;

class ViewAllConsultantInviteeWithPendingReportPayload
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
     * @var ConsultantInviteeFilter
     */
    protected $consultantInviteeFilter;
    public $result;

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getConsultantInviteeFilter(): ConsultantInviteeFilter
    {
        return $this->consultantInviteeFilter;
    }

    public function __construct(?int $page, ?int $pageSize, ConsultantInviteeFilter $consultantInviteeFilter)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->consultantInviteeFilter = $consultantInviteeFilter;
    }

}
