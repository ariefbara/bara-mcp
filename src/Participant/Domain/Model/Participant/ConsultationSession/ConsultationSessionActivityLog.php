<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationSession,
    SharedModel\ActivityLog,
    SharedModel\ContainActvityLog
};

class ConsultationSessionActivityLog implements ContainActvityLog
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
    
    public function __construct(ConsultationSession $consultationSession, string $id, string $message)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message);
    }

    public function setOperator(TeamMembership $teamMember): void
    {
        $this->activityLog->setOperator($teamMember);
    }

}
