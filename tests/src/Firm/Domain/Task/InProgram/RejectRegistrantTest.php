<?php

namespace Firm\Domain\Task\InProgram;

use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class RejectRegistrantTest extends TaskInProgramTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRegistrantDependency();
        
        $this->task = new RejectRegistrant($this->registrantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program, $this->registrantId);
    }
    public function test_execute_rejectRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('reject');
        $this->execute();
    }
    public function test_execute_assertRegistrantManageableInProgram()
    {
        $this->registrant->expects($this->once())
                ->method('assertManageableInProgram')
                ->with($this->program);
        $this->execute();
    }
}
