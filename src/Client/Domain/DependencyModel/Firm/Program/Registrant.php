<?php

namespace Client\Domain\DependencyModel\Firm\Program;

use Client\Domain\DependencyModel\Firm\Program;
use SharedContext\Domain\ValueObject\RegistrationStatus;

class Registrant
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
     * @var RegistrationStatus
     */
    protected $status;

    protected function __construct()
    {
        
    }
    
    public function isActiveRegistrationCorrespondWithProgram(Program $program): bool
    {
        return !$this->status->isConcluded() && $this->program === $program;
    }
    
    public function cancel(): void
    {
        $this->status = $this->status->cancel();
    }

}
