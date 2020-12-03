<?php

namespace User\Domain\DependencyModel\Firm\Program;

use DateTimeImmutable;
use User\Domain\DependencyModel\Firm\Program;

class Participant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $enrolledTime;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    
    public function isActiveParticipantOfProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

}
