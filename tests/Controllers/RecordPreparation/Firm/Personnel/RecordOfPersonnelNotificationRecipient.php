<?php

namespace Tests\Controllers\RecordPreparation\Firm\Personnel;

use Tests\Controllers\RecordPreparation\{
    Firm\RecordOfPersonnel,
    Record,
    Shared\RecordOfNotification
};

class RecordOfPersonnelNotificationRecipient implements Record
{

    /**
     *
     * @var RecordOfPersonnel
     */
    public $personnel;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;
    public $readStatus;
    public $notifiedTime;

    public function __construct(RecordOfPersonnel $personnel, RecordOfNotification $notification)
    {
        $this->personnel = $personnel;
        $this->notification = $notification;
        $this->id = $notification->id;
        $this->readStatus = false;
        $this->notifiedTime = (new \DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "Personnel_id" => $this->personnel->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
            "readStatus" => $this->readStatus,
            "notifiedTime" => $this->notifiedTime,
        ];
    }

}
