<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Firm\Domain\Task\Dependency\Firm\Program\RegistrantRepository;

class RejectRegistrant implements TaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;

    public function __construct(RegistrantRepository $registrantRepository)
    {
        $this->registrantRepository = $registrantRepository;
    }

    /**
     * 
     * @param Program $program
     * @param string $payload registrantId
     * @return void
     */
    public function execute(Program $program, $payload): void
    {
        $registrant = $this->registrantRepository->aRegistrantOfId($payload);
        $registrant->assertManageableInProgram($program);
        
        $registrant->reject();
    }

}
