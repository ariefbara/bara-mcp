<?php

namespace Tests\Controllers\RecordPreparation\Firm;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class RecordOfFirmFileInfo implements Record
{
    /**
     *
     * @var RecordOfFirm
     */
    public $firm;
    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id;
    public $removed;
    
    public function __construct(RecordOfFirm $firm, RecordOfFileInfo $fileInfo)
    {
        $this->firm = $firm;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            "Firm_id" => $this->firm->id,
            "FileInfo_id" => $this->fileInfo->id,
            "id" => $this->id,
            "removed" => $this->removed,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->fileInfo->insert($connection);
        $connection->table("FirmFileInfo")->insert($this->toArrayForDbEntry());
    }

}
