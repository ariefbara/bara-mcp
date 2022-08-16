<?php

namespace Firm\Domain\Task\InProgram;

use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class AcceptRegisteredParticipantTest extends TaskInProgramTestBase
{
    protected $dispatcher;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareParticipantDependency();
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->task = new AcceptRegisteredParticipant($this->participantRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program, $this->participantId);
    }
    public function test_execute_acceptRegisteredParticipant()
    {
        $this->participant->expects($this->once())
                ->method('acceptRegistrant');
        $this->execute();
    }
    public function test_execute_dispatchPartcipant()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->participant);
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
