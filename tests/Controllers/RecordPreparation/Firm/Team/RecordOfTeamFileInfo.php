<?php

namespace Tests\Controllers\RecordPreparation\Firm\Team;

use Tests\Controllers\RecordPreparation\{
    Firm\RecordOfTeam,
    Record,
    Shared\RecordOfFileInfo
};

class RecordOfTeamFileInfo implements Record
{

    /**
     *
     * @var RecordOfTeam
     */
    public $team;

    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id;
    public $removed;

    public function __construct(RecordOfTeam $team, RecordOfFileInfo $fileInfo)
    {
        $this->team = $team;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Team_id" => $this->team->id,
            "FileInfo_id" => $this->fileInfo->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }

}
