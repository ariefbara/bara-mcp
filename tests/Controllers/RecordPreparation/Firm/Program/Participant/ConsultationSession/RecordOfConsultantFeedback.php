<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationSession,
    Record,
    Shared\RecordOfFormRecord
};

class RecordOfConsultantFeedback implements Record
{

    /**
     *
     * @var RecordOfConsultationSession
     */
    public $consultationSession;

    /**
     *
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    public $participantRating;

    function __construct(?RecordOfConsultationSession $consultationSession, ?RecordOfFormRecord $formRecord, $index)
    {
        $this->consultationSession = $consultationSession;
        $this->formRecord = $formRecord;
        $this->id = isset($formRecord)? $formRecord->id: "consultantFeedback-$index-id";
        $this->participantRating = 2;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSession_id" => isset($this->consultationSession)? $this->consultationSession->id: null,
            "FormRecord_id" => isset($this->formRecord)? $this->formRecord->id: null,
            "id" => $this->id,
            "participantRating" => $this->participantRating,
        ];
    }

}
