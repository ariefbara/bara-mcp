<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationSession,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfConsultationSessionActivityLog implements Record
{
    /**
     *
     * @var RecordOfConsultationSession
     */
    public $consultationSession;
    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;
    
    public function __construct(RecordOfConsultationSession $consultationSession, RecordOfActivityLog $activityLog)
    {
        $this->consultationSession = $consultationSession;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSession_id" => $this->consultationSession->id,
            "id" => $this->id,
            "ActivityLog_id" => $this->activityLog->id,
        ];
    }

}
