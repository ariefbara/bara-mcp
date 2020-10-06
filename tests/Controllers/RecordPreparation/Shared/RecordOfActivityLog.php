<?php

namespace Tests\Controllers\RecordPreparation\Shared;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfActivityLog implements Record
{
    public $id;
    public $message;
    public $occuredTime;
    
    public function __construct($index)
    {
        $this->id = "activityLog-$index-id";
        $this->message = "activity log $index message";
        $this->occuredTime = (new DateTimeImmutable())->format("Y-m-d H:i:s");
    }

    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "message" => $this->message,
            "occuredTime" => $this->occuredTime,
        ];
    }

}
