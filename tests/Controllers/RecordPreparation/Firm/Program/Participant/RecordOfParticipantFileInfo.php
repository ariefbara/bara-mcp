<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Record,
    Shared\RecordOfFileInfo
};

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
    public $id, $removed = false;
    
    function __construct(RecordOfParticipant $partipant, RecordOfFileInfo $fileInfo)
    {
        $this->partipant = $partipant;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Participant_id" => $this->partipant->id,
            "id" => $this->id,
            "FileInfo_id" => $this->fileInfo->id,
            "removed" => $this->removed,
        ];
    }

}
