<?php

namespace Client\Domain\DependencyModel\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program;

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
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    
    public function isActiveParticipationCorrespondWithProgram(Program $program): bool
    {
        return $this->active && $this->program === $program;
    }

}
