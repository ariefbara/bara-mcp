<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Coordinator;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Tests\TestBase;

class ExecuteTaskInProgramTest extends TestBase
{
    protected $coordinatorRepository;
    protected $service;
    
    protected $coordinator;
    protected $firmId = 'firm-id', $personnelId = 'personnel-id', $coordinatorId = 'coordinator-id';
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->service = new ExecuteTaskInProgram($this->coordinatorRepository);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorBelongsToPersonnel')
                ->with($this->firmId, $this->personnelId, $this->coordinatorId)
                ->willReturn($this->coordinator);
        
        $this->task = $this->buildMockOfInterface(ITaskInProgramExecutableByCoordinator::class);
    }
    
    public function execute()
    {
        $this->service->execute($this->firmId, $this->personnelId, $this->coordinatorId, $this->task);
    }
    public function test_execute_coordinatorExecuteTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->task);
        $this->execute();
    }
    
}
