<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use DateTime;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfComment implements Record
{
    /**
     *
     * @var RecordOfComment
     */
    public $parent = null;
    public $id, $message, $submitTime, $removed = false;
    
    function __construct(string $index, ?RecordOfComment $parent = null)
    {
        $this->parent = $parent;
        $this->id = "comment-$index";
        $this->message = "comment message $index";
        $this->submitTime = (new DateTime())->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            "id" => $this->id,
            "parent_id" => empty($this->parent)? null: $this->parent->id,
            "message" => $this->message,
            "submitTime" => $this->submitTime,
            "removed" => $this->removed,
        ];
    }

}
