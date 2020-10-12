<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationRequest;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\RecordOfConsultationRequest,
    Record,
    Shared\RecordOfNotification
};

class RecordOfConsultationRequestNotification implements Record
{

    /**
     *
     * @var RecordOfConsultationRequest
     */
    public $consultationRequest;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;

    public function __construct(RecordOfConsultationRequest $consultationRequest, RecordOfNotification $notification)
    {
        $this->consultationRequest = $consultationRequest;
        $this->notification = $notification;
        $this->id = $notification->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "ConsultationRequest_id" => $this->consultationRequest->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
        ];
    }

}
