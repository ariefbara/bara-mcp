<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program;

use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfSponsor implements Record
{

    /**
     * 
     * @var RecordOfProgram
     */
    public $program;
    public $id;
    public $disabled;
    public $name;
    public $website;

    /**
     * 
     * @var RecordOfFirmFileInfo
     */
    public $logo;

    public function __construct(RecordOfProgram $program, $index)
    {
        $this->program = $program;
        $this->id = "sponsor-$index-id";
        $this->disabled = false;
        $this->name = "sponsor $index name";
        $this->website = "sponsor.$index.web";
        $this->logo = null;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Program_id" => $this->program->id,
            "id" => $this->id,
            "disabled" => $this->disabled,
            "name" => $this->name,
            "website" => $this->website,
            "FirmFileInfo_idOfLogo" => isset($this->logo) ? $this->logo->id : null,
        ];
    }
    
    public function insert(\Illuminate\Database\ConnectionInterface $connection): void
    {
        $connection->table("Sponsor")->insert($this->toArrayForDbEntry());
    }

}
