<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\Participant\Task;

class ConsultantTask
{

    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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

    public function getConsultant(): Consultant
    {
        return $this->consultant;
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
