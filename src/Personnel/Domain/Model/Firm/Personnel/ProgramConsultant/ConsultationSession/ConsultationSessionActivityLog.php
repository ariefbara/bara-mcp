<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Personnel\ProgramConsultant\ConsultationSession,
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
            ConsultationSession $consultationSession, string $id, string $message, ProgramConsultant $consultant)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->activityLog = new ActivityLog($id, $message, $consultant);
    }

}
