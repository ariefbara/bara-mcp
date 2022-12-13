<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Program\Participant\TaskData;
use SharedContext\Domain\ValueObject\LabelData;

class UpdateTaskPayload
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var TaskData
     */
    protected $taskData;

    public function __construct(string $id, TaskData $taskData)
    {
        $this->id = $id;
        $this->taskData = $taskData;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTaskData(): TaskData
    {
        return $this->taskData;
    }

}
