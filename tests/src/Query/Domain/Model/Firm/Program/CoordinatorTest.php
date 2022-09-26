<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{
    protected $coordinator, $program, $programId = 'programId';
    protected $taskInProgram;
    protected $programTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = new TestableCoordinator();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->coordinator->program = $this->program;
        
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByCoordinator::class);
        $this->programTask = $this->buildMockOfInterface(ProgramTaskExecutableByCoordinator::class);
    }
    
    protected function executeTaskInProgram()
    {
        $this->coordinator->executeTaskInProgram($this->taskInProgram);
    }
    public function test_executeTaskInProgram_executeTask()
    {
        $this->taskInProgram->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->program);
        $this->executeTaskInProgram();
    }
    public function test_executeTaskInProgram_inactiveCoordiantor()
    {
        $this->coordinator->active = false;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTaskInProgram();
        }, 'Forbidden', 'forbidden: only active coordinator can make this request');
    }
    
    protected function executeProgramTask()
    {
        $this->program->expects($this->any())
                ->method('getId')
                ->willReturn($this->programId);
        $this->coordinator->executeProgramTask($this->programTask, $this->payload);
    }
    public function test_executeProgramTask_executeTask()
    {
        $this->programTask->expects($this->once())
                ->method('execute')
                ->with($this->programId, $this->payload);
        $this->executeProgramTask();
    }
    public function test_executeProgramTask_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeProgramTask();
        }, 'Forbidden', 'forbidden: only active coordinator can make this request');
    }
}

class TestableCoordinator extends Coordinator
{
    public $program;
    public $id = 'coordinator-id';
    public $personnel;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
