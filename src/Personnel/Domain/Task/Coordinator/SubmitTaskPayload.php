<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Program\Participant\TaskData;

class SubmitTaskPayload
{

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var TaskData
     */
    protected $taskData;
    public $submittedTaskId;

    public function __construct(string $participantId, TaskData $taskData)
    {
        $this->participantId = $participantId;
        $this->taskData = $taskData;
    }

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getTaskData(): TaskData
    {
        return $this->taskData;
    }

}
