<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Tests\TestBase;

class ExecuteProgramTaskTest extends TestBase
{

    protected $coordinatorRepository;
    protected $coordinator;
    protected $firmId = 'firmId', $personnelId = 'personnelId', $programId = 'programId';
    protected $service;
    //
    protected $task, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinatorRepository = $this->buildMockOfInterface(CoordinatorRepository::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);

        $this->service = new ExecuteProgramTask($this->coordinatorRepository);

        $this->task = $this->buildMockOfInterface(TaskInProgramExecutableByCoordinator::class);
    }
    
    protected function execute()
    {
        $this->coordinatorRepository->expects($this->any())
                ->method('aCoordinatorCorrespondWithProgram')
                ->with($this->firmId, $this->personnelId, $this->programId)
                ->willReturn($this->coordinator);
        $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->task, $this->payload);
    }
    public function test_execute_coorddinatorExecuteProgramTask()
    {
        $this->coordinator->expects($this->once())
                ->method('executeProgramTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }

}
