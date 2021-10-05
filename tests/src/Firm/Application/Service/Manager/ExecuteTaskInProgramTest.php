<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ITaskInProgramExecutableByManager;
use Firm\Domain\Model\Firm\Program;
use Tests\src\Firm\Application\Service\Manager\ManagerTestBase;

class ExecuteTaskInProgramTest extends ManagerTestBase
{
    protected $programRepository, $program, $programId = 'program-id';
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->service = new ExecuteTaskInProgram($this->managerRepository, $this->programRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskInProgramExecutableByManager::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->programId, $this->task);
    }
    public function test_execute_managerExecuteTask()
    {
        $this->manager->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->program, $this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->managerRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
