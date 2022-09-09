<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet;

use DateTime;
use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfWorksheet;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfComment implements Record
{
    /**
     *
     * @var RecordOfWorksheet
     */
    public $worksheet;
    /**
     *
     * @var RecordOfComment
     */
    public $parent = null;
    public $id, $message, $submitTime, $removed = false;
    
    function __construct(RecordOfWorksheet $worksheet, string $index, ?RecordOfComment $parent = null)
    {
        $this->worksheet = $worksheet;
        $this->parent = $parent;
        $this->id = "comment-$index";
        $this->message = "comment message $index";
        $this->submitTime = (new DateTime())->format('Y-m-d H:i:s');
    }

    public function toArrayForDbEntry()
    {
        return [
            "Worksheet_id" => $this->worksheet->id,
            "id" => $this->id,
            "parent_id" => empty($this->parent)? null: $this->parent->id,
            "message" => $this->message,
            "submitTime" => $this->submitTime,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('Comment')->insert($this->toArrayForDbEntry());
    }

}
