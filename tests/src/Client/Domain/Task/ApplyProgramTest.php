<?php

namespace Client\Domain\Task;

use Resources\Application\Event\AdvanceDispatcher;
use Tests\src\Client\Domain\Task\ClientTaskTestBase;

class ApplyProgramTest extends ClientTaskTestBase
{
    protected $dispatcher;
    protected $task;
    protected $programId = 'programId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->task = new ApplyProgram($this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->client, $this->programId);
    }
    public function test_execute_clientApplyToProgram()
    {
        $this->client->expects($this->once())
                ->method('applyToProgram')
                ->with($this->programId);
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
