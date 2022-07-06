<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;

class AcceptRegistrant implements TaskInProgramExecutableByCoordinator
{
    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;


    public function execute(\Firm\Domain\Model\Firm\Program $program, $payload): void
    {
        
    }

}
