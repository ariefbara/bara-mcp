<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfClient,
    Record,
    Shared\RecordOfNotification
};

class RecordOfClientNotificationRecipient implements Record
{

    /**
     *
     * @var RecordOfClient
     */
    public $client;

    /**
     *
     * @var RecordOfNotification
     */
    public $notification;
    public $id;
    public $readStatus;
    public $notifiedTime;

    public function __construct(RecordOfClient $client, RecordOfNotification $notification)
    {
        $this->client = $client;
        $this->notification = $notification;
        $this->id = $notification->id;
        $this->readStatus = false;
        $this->notifiedTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "Client_id" => $this->client->id,
            "Notification_id" => $this->notification->id,
            "id" => $this->id,
            "readStatus" => $this->readStatus,
            "notifiedTime" => $this->notifiedTime,
        ];
    }

}
