<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $program, $programId = 'programId';
    
    protected $taskInProgramExecutableByConsultant;
    protected $programTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultant->program = $this->program;
        
        $this->taskInProgramExecutableByConsultant = $this->buildMockOfInterface(ITaskInProgramExecutableByConsultant::class);
        $this->programTask = $this->buildMockOfInterface(ProgramTaskExecutableByConsultant::class);
    }
    
    protected function executeTaskInProgram()
    {
        $this->consultant->executeTaskInProgram($this->taskInProgramExecutableByConsultant);
    }
    public function test_execute_executeTask()
    {
        $this->taskInProgramExecutableByConsultant->expects($this->once())
                ->method('executeTaskInProgram')
                ->with($this->program);
        $this->executeTaskInProgram();
    }
    public function test_execute_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeTaskInProgram();
        }, 'Forbidden', 'forbidden: only active consultant can make this request');
    }
    
    protected function executeProgramTask()
    {
        $this->program->expects($this->any())
                ->method('getId')
                ->willReturn($this->programId);
        $this->consultant->executeProgramTask($this->programTask, $this->payload);
    }
    public function test_executeProgramTask_executeTask()
    {
        $this->programTask->expects($this->once())
                ->method('execute')
                ->with($this->programId, $this->payload);
        $this->executeProgramTask();
    }
    public function test_executeProgramTask_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeProgramTask();
        }, 'Forbidden', 'forbidden: only active consultant can make this request');
    }
}

class TestableConsultant extends Consultant
{
    public $program;
    public $id;
    public $personnel;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
