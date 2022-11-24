<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class RecordOfParticipantFileInfo implements Record
{

    /**
     *
     * @var RecordOfParticipant
     */
    public $partipant;

    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id;

    function __construct(RecordOfParticipant $partipant, RecordOfFileInfo $fileInfo)
    {
        $this->partipant = $partipant;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->partipant->id,
            "id" => $this->id,
            "FileInfo_id" => $this->fileInfo->id,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $this->fileInfo->insert($connection);
        $connection->table('ParticipantFileInfo')->insert($this->toArrayForDbEntry());
    }

}
