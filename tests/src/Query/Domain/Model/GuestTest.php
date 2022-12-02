<?php

namespace Query\Domain\Model;

use Query\Domain\Task\Guest\GuestTask;
use Tests\TestBase;

class GuestTest extends TestBase
{

    protected $guest;
    //
    protected $task, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->guest = new TestableGuest();
        //
        $this->task = $this->buildMockOfInterface(GuestTask::class);
    }

    protected function executeTask()
    {
        $this->guest->executeTask($this->task, $this->payload);
    }

    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->payload);
        $this->executeTask();
    }

}

class TestableGuest extends Guest
{
    
}
