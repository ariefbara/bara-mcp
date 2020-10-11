<?php

namespace Participant\Domain\Model\Participant\ConsultationRequest;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationRequest,
    SharedModel\ActivityLog
};

class ConsultationRequestActivityLog
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

    public function __construct(
            ConsultationRequest $consultationRequest, string $id, string $message, ?TeamMembership $teamMember)
    {
        $this->consultationRequest = $consultationRequest;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $teamMember);
    }

}
