<?php

namespace Query\Application\Service\Guest;

use Query\Domain\Model\Guest;
use Query\Domain\Task\Guest\GuestTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $guestRepository;
    protected $service;
    protected $guest;
    //
    protected $task, $payload = 'string represent payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->guestRepository = $this->buildMockOfClass(GuestRepository::class);
        $this->service = new ExecuteTask($this->guestRepository);
        
        $this->guest = $this->buildMockOfClass(Guest::class);
        //
        $this->task = $this->buildMockOfInterface(GuestTask::class);
    }
    
    protected function executeTask()
    {
        $this->guestRepository->expects($this->any())
                ->method('get')
                ->willReturn($this->guest);
        
        $this->service->execute($this->task, $this->payload);
    }
    public function test_executeTask_guestExecuteTask()
    {
        $this->guest->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->executeTask();
    }
}
