<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantInviteeRepository;

class ViewAllConsultantInviteeWithPendingReport implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var ConsultantInviteeRepository
     */
    protected $consultantInviteeRepository;

    /**
     * 
     * @var ViewAllConsultantInviteeWithPendingReportPayload
     */
    protected $payload;

    public function __construct(ConsultantInviteeRepository $consultantInviteeRepository,
            ViewAllConsultantInviteeWithPendingReportPayload $payload)
    {
        $this->consultantInviteeRepository = $consultantInviteeRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->payload->result = $this->consultantInviteeRepository->allInvitationWithPendingReportForPersonnel(
                $personnelId, $this->payload->getPage(), $this->payload->getPageSize(),
                $this->payload->getConsultantInviteeFilter());
    }

}
