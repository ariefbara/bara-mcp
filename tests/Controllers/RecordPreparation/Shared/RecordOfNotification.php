<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use Tests\Controllers\RecordPreparation\Record;

class RecordOfNotification implements Record
{
    public $id;
    public $message;
    
    public function __construct($index)
    {
        $this->id = "notification-$index-id";
        $this->message = "notification $index message";
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "message" => $this->message,
        ];
    }

}
