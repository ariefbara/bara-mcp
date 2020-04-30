<?php

namespace Tests\Controllers\RecordPreparation\Firm\Personnel;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfPersonnel,
    Shared\RecordOfFileInfo,
    Shared\RecordOfFormRecord
};

class RecordOfPersonnelFileInfo implements Record
{
    /**
     *
     * @var RecordOfPersonnel
     */
    public $personnel;
    /**
     *
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id, $removed = false;
    
    function __construct(RecordOfPersonnel $personnel, RecordOfFileInfo $fileInfo)
    {
        $this->personnel = $personnel;
        $this->fileInfo = $fileInfo;
        $this->id = $fileInfo->id;
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            "Personnel_id" => $this->personnel->id,
            "id" => $this->id,
            "FileInfo_id" => $this->fileInfo->id,
            "removed" => $this->removed,
        ];
    }

}
