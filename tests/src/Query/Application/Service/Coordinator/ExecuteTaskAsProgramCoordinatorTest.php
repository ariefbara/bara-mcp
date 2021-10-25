<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Tests\TestBase;

class ExecuteTaskAsProgramCoordinatorTest extends TestBase
{

    protected $coordinatorRepository;
    protected $coordinator;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programId = 'programId';
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorCorrespondWithProgram')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);

        $this->service = new ExecuteTaskAsProgramCoordinator($this->coordinatorRepository);

        $this->task = $this->buildMockOfInterface(ITaskInProgramExecutableByCoordinator::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->task);
    }
    public function test_execute_coordinatorExecuteTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->task);
        $this->execute();
    }

}
