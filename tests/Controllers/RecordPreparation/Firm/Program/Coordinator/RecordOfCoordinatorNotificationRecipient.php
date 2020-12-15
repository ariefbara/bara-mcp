<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Coordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfNotification;

class RecordOfCoordinatorNotificationRecipient implements Record
{
    /**
     * 
     * @var RecordOfCoordinator
     */
    public $coordinator;
    /**
     * 
     * @var RecordOfNotification
     */
    public $notification;
    public $id;
    public $readStatus;
    public $notifiedTime;
    
    function __construct(RecordOfCoordinator $coordinator, ?RecordOfNotification $notification, $index)
    {
        $this->coordinator = $coordinator;
        $this->notification = isset($notification)? $notification: new RecordOfNotification($index);
        $this->id = $this->notification->id;
        $this->readStatus = false;
        $this->notifiedTime = (new DateTimeImmutable("-24 hours"))->format("Y-m-d H:i:s");
    }
    
    public function toArrayForDbEntry()
    {
        return [
            "Coordinator_id" => $this->coordinator->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
            "readStatus" => $this->readStatus,
            "notifiedTime" => $this->notifiedTime,
        ];
    }

}
