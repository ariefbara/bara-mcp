<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfConsultationSession,
    Record,
    Shared\RecordOfFormRecord
};

class RecordOfParticipantFeedback implements Record
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
    public $mentorRating;

    function __construct(?RecordOfConsultationSession $consultationSession, ?RecordOfFormRecord $formRecord, $index = null)
    {
        $this->consultationSession = $consultationSession;
        $this->formRecord = $formRecord;
        $this->id = isset($formRecord)? $formRecord->id: "participantFeedback-$index-id";
        $this->mentorRating = 1;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSession_id" => isset($this->consultationSession)? $this->consultationSession->id: null,
            "FormRecord_id" => isset($this->formRecord)? $this->formRecord->id: null,
            "id" => $this->id,
            "mentorRating" => $this->mentorRating,
        ];
    }

}
