<?php

namespace Tests\Controllers\RecordPreparation\Shared\Mentoring;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class RecordOfMentorReport implements Record
{

    /**
     * 
     * @var RecordOfMentoring
     */
    public $mentoring;

    /**
     * 
     * @var RecordOfFormRecord
     */
    public $formRecord;
    public $id;
    public $participantRating;

    public function __construct(RecordOfMentoring $mentoring, ?RecordOfFormRecord $formRecord, $index)
    {
        $this->mentoring = $mentoring;
        $this->formRecord = $formRecord;
        $this->id = "mentorReport-$index-id";
        $this->participantRating = 7;
    }

    public function toArrayForDbEntry()
    {
        return [
            'id' => $this->id,
            'participantRating' => $this->participantRating,
            'Mentoring_id' => $this->mentoring->id,
            'FormRecord_id' => $this->formRecord ? $this->formRecord->id : null,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        if ($this->formRecord) {
            $this->formRecord->insert($connection);
        }
        $connection->table('MentorReport')->insert($this->toArrayForDbEntry());
    }

}
