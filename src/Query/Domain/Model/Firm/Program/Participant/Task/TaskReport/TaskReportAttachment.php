<?php

namespace Query\Domain\Model\Firm\Program\Participant\Task\TaskReport;

use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport;

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

    protected function __construct()
    {
        
    }

    public function getTaskReport(): TaskReport
    {
        return $this->taskReport;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getParticipantFileInfo(): ParticipantFileInfo
    {
        return $this->participantFileInfo;
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

}
