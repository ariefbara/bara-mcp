<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\RecordOfKeyResultProgressReport;
use Tests\Controllers\RecordPreparation\Record;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class RecordOfKeyResultProgressReportAttachment implements Record
{

    /**
     * 
     * @var RecordOfKeyResultProgressReport
     */
    public $keyResultProgressReport;

    /**
     * 
     * @var RecordOfFileInfo
     */
    public $fileInfo;
    public $id;
    public $removed;

    public function __construct(RecordOfKeyResultProgressReport $keyResultProgressReport, RecordOfFileInfo $fileInfo,
            $id)
    {
        $this->keyResultProgressReport = $keyResultProgressReport;
        $this->fileInfo = $fileInfo;
        $this->id = "attachment-$id-id";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'KeyResultProgressReport_id' => $this->keyResultProgressReport->id,
            'FileInfo_id' => $this->fileInfo->id,
            'id' => $this->id,
            'removed' => false,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('KeyResultProgressReportAttachment')->insert($this->toArrayForDbEntry());
    }

}
