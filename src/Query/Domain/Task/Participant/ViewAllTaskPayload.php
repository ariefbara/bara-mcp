<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilter;

class ViewAllTaskPayload
{

    /**
     * 
     * @var TaskListFilter
     */
    protected $taskListFilter;
    public $result;

    public function __construct(TaskListFilter $taskListFilter)
    {
        $this->taskListFilter = $taskListFilter;
    }

    public function getTaskListFilter(): TaskListFilter
    {
        return $this->taskListFilter;
    }

}
