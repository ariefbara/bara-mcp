<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $program;
    
    protected $taskInProgramExecutableByConsultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultant->program = $this->program;
        
        $this->taskInProgramExecutableByConsultant = $this->buildMockOfInterface(ITaskInProgramExecutableByConsultant::class);
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
