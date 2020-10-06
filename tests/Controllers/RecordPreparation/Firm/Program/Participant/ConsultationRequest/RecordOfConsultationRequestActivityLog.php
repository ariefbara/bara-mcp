<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationRequest;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationRequest,
    Record,
    Shared\RecordOfActivityLog
};

class RecordOfConsultationRequestActivityLog implements Record
{
    /**
     *
     * @var RecordOfConsultationRequest
     */
    public $consultationRequest;
    /**
     *
     * @var RecordOfActivityLog
     */
    public $activityLog;
    public $id;
    
    public function __construct(RecordOfConsultationRequest $consultationRequest, RecordOfActivityLog $activityLog)
    {
        $this->consultationRequest = $consultationRequest;
        $this->activityLog = $activityLog;
        $this->id = $activityLog->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationRequest_id" => $this->consultationRequest->id,
            "id" => $this->id,
            "ActivityLog_id" => $this->activityLog->id,
        ];
    }

}
