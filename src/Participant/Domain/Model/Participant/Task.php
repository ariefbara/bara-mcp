<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\Task\TaskReport;
use Participant\Domain\Model\Participant\Task\TaskReportData;
use Resources\Exception\RegularException;
use Resources\Uuid;

class Task
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var TaskReport
     */
    protected $taskReport;

    protected function __construct()
    {
        
    }

    public function submitReport(TaskReportData $taskReportData): void
    {
        if ($this->taskReport) {
            $this->taskReport->update($taskReportData);
        } else {
            $this->taskReport = new TaskReport($this, Uuid::generateUuid4(), $taskReportData);
        }
    }
    
    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant) {
            throw RegularException::forbidden('unmanaged task, can only manage own task');
        }
    }

}
