<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\ProgramRepository;
use Firm\Domain\Model\Firm\IProgramTask;
use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $programRepository, $program, $programId = 'program-id';
    protected $service;
    protected $task, $payload = 'random string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->service = new ExecuteTask($this->programRepository);
        
        $this->task = $this->buildMockOfInterface(IProgramTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programId, $this->task, $this->payload);
    }
    public function test_execute_programExecuteTask()
    {
        $this->program->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
