<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationSession,
    SharedModel\ActivityLog
};

class ConsultationSessionActivityLog
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

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
            ConsultationSession $consultationSession, string $id, string $message, ?TeamMembership $teamMember)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $teamMember);
    }

}
