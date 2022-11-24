<?php

namespace Participant\Domain\Model\Participant\Task\TaskReport;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\Task\TaskReport;
use Participant\Domain\Model\Participant\Task\TaskReportData;

class TaskReportAttachment
{

    /**
     * 
     * @var TaskReport
     */
    protected $taskReport;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ParticipantFileInfo
     */
    protected $participantFileInfo;
    
    /**
     * 
     * @var bool
     */
    protected $removed;

    public function isRemoved()
    {
        return $this->removed;
    }

    public function __construct(TaskReport $taskReport, string $id, ParticipantFileInfo $participantFileInfo)
    {
        $this->taskReport = $taskReport;
        $this->id = $id;
        $this->participantFileInfo = $participantFileInfo;
        $this->removed = false;
    }

    /**
     * 
     * @param TaskReportData $data
     * @return bool true if attachment updated (removed), false if no change occured
     */
    public function update(TaskReportData $data): bool
    {
        if ($data->detachParticipantFileInfo($this->participantFileInfo)) {
            return false;
        } else {
            $this->removed = true;
            return true;
        }
    }

}
