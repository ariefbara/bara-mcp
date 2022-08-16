<?php

namespace Client\Domain\Task;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\Task\Repository\Firm\ProgramRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Client\Domain\Task\ClientTaskTestBase;

class ApplyProgramTest extends ClientTaskTestBase
{
    protected $programRepository, $program, $programId = 'program-id';
    protected $dispatcher;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->task = new ApplyProgram($this->programRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->client, $this->programId);
    }
    public function test_execute_clientApplyToProgram()
    {
        $this->client->expects($this->once())
                ->method('applyToProgram')
                ->with($this->program);
        $this->execute();
    }
    public function test_execute_dispatchClient()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->client);
        $this->execute();
    }
}
