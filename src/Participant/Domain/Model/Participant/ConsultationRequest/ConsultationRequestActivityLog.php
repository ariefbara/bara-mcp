<?php

namespace Participant\Domain\Model\Participant\ConsultationRequest;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationRequest,
    SharedModel\ActivityLog,
    SharedModel\ContainActvityLog
};

class ConsultationRequestActivityLog implements ContainActvityLog
{

    /**
     *
     * @var ConsultationRequest
     */
    protected $consultationRequest;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function __construct(ConsultationRequest $consultationRequest, string $id, string $message)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message);
    }

    public function setOperator(TeamMembership $teamMember): void
    {
        $this->activityLog->setOperator($teamMember);
    }

}
