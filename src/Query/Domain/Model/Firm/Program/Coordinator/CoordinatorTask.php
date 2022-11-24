<?php

namespace Query\Domain\Model\Firm\Program\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\Participant\Task;

class CoordinatorTask
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Task
     */
    protected $task;

    protected function __construct()
    {
        
    }

    public function getCoordinator(): Coordinator
    {
        return $this->coordinator;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

}
