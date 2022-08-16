<?php

namespace Firm\Domain\Task\InProgram;

use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class RejectRegisteredParticipantTest extends TaskInProgramTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareParticipantDependency();
        
        $this->task = new RejectRegisteredParticipant($this->participantRepository);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program, $this->participantId);
    }
    public function test_execute_rejectRegisteredParticipant()
    {
        $this->participant->expects($this->once())
                ->method('rejectRegistrant');
        $this->execute();
    }
    public function test_execute_assertParticipantManageableInProgram()
    {
        $this->participant->expects($this->once())
                ->method('assertManageableInProgram')
                ->with($this->program);
        $this->execute();
    }
}
