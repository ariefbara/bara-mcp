<?php

namespace Tests\Controllers\RecordPreparation\User;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\{
    Record,
    RecordOfUser,
    Shared\RecordOfNotification
};

class RecordOfUserNotificationRecipient implements Record
{

    /**
     *
     * @var RecordOfUser
     */
    public $user;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;
    public $readStatus;
    public $notifiedTime;

    public function __construct(RecordOfUser $user, RecordOfNotification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
        $this->id = $notification->id;
        $this->readStatus = false;
        $this->notifiedTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "User_id" => $this->user->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
            "readStatus" => $this->readStatus,
            "notifiedTime" => $this->notifiedTime,
        ];
    }

}
