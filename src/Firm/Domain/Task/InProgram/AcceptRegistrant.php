<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Firm\Domain\Task\Dependency\Firm\Program\RegistrantRepository;
use Resources\Application\Event\AdvanceDispatcher;

class AcceptRegistrant implements TaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var RegistrantRepository
     */
    protected $registrantRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(RegistrantRepository $registrantRepository, AdvanceDispatcher $dispatcher)
    {
        $this->registrantRepository = $registrantRepository;
        $this->dispatcher = $dispatcher;
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
        
        $registrant->accept();
        $this->dispatcher->dispatch($registrant);
    }

}
