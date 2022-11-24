<?php

namespace Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\TaskReport;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantFileInfo;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfTaskReportAttachment implements Record
{

    /**
     * 
     * @var RecordOfTaskReport
     */
    public $taskReport;

    /**
     * 
     * @var RecordOfParticipantFileInfo
     */
    public $participantFileInfo;
    public $id;
    public $removed;

    public function __construct(RecordOfTaskReport $taskReport, RecordOfParticipantFileInfo $participantFileInfo, $id)
    {
        $this->taskReport = $taskReport;
        $this->participantFileInfo = $participantFileInfo;
        $this->id = "taskReportAttachment-$id-index";
        $this->removed = false;
    }

    public function toArrayForDbEntry()
    {
        return [
            'TaskReport_id' => $this->taskReport->id,
            'ParticipantFileInfo_id' => $this->participantFileInfo->id,
            'id' => $this->id,
            'removed' => $this->removed,
        ];
    }

    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('TaskReportAttachment')->insert($this->toArrayForDbEntry());
    }

}
