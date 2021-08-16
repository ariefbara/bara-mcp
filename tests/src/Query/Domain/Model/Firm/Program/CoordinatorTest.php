<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class CoordinatorTest extends TestBase
{
    protected $coordinator, $program;
    protected $taskInProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = new TestableCoordinator();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->coordinator->program = $this->program;
        
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByCoordinator::class);
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
