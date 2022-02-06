<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Mission\LearningMaterial;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfLearningMaterial;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfLearningAttachment implements Record
{
    /**
     * 
     * @var RecordOfLearningMaterial
     */
    public $learningMaterial;
    /**
     * 
     * @var RecordOfFirmFileInfo
     */
    public $firmFileInfo;
    public $id;
    public $disabled;
    
    public function __construct(RecordOfLearningMaterial $learningMaterial, RecordOfFirmFileInfo $firmFileInfo, $index)
    {
        $this->learningMaterial = $learningMaterial;
        $this->firmFileInfo = $firmFileInfo;
        $this->id = "learningAttachment-$index-id";
        $this->disabled = false;
    }

    
    public function toArrayForDbEntry()
    {
        return [
            'LearningMaterial_id' => $this->learningMaterial->id,
            'FirmFileInfo_id' => $this->firmFileInfo->id,
            'id' => $this->id,
            'disabled' => $this->disabled,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('LearningAttachment')->insert($this->toArrayForDbEntry());
    }

}
