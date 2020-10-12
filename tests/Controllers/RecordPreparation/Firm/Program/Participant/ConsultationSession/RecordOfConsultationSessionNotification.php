<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfConsultationSession,
    Record,
    Shared\RecordOfNotification
};

class RecordOfConsultationSessionNotification implements Record
{
    /**
     *
     * @var RecordOfConsultationSession
     */
    public $consultationSession;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;
    
    public function __construct(RecordOfConsultationSession $consultationSession, RecordOfNotification $notification)
    {
        $this->consultationSession = $consultationSession;
        $this->notification = $notification;
        $this->id = $notification->id;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "ConsultationSession_id" => $this->consultationSession->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
        ];
    }

}
