<?php

namespace Firm\Domain\Task\InProgram;

use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class AcceptRegistrantTest extends TaskInProgramTestBase
{
    protected $dispatcher;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRegistrantDependency();
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->task = new AcceptRegistrant($this->registrantRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program, $this->registrantId);
    }
    public function test_execute_acceptRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('accept');
        $this->execute();
    }
    public function test_execute_dispatchRegistrant()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->registrant);
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
