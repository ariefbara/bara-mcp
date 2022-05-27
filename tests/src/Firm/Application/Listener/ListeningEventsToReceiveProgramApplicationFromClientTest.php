<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Task\Responsive\ReceiveProgramApplicationFromClient;
use Firm\Domain\Task\Responsive\ReceiveProgramApplicationPayload;
use Tests\TestBase;

class ListeningEventsToReceiveProgramApplicationFromClientTest extends TestBase
{
    protected $service;
    protected $task;
    protected $listener;
    protected $event, $programId = 'program-id', $clientId = 'client-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteResponsiveTask::class);
        $this->task = $this->buildMockOfClass(ReceiveProgramApplicationFromClient::class);
        $this->listener = new ListeningEventsToReceiveProgramApplicationFromClient($this->service, $this->task);
        $this->event = new \Client\Domain\Event\ClientHasAppliedToProgram($this->clientId, $this->programId);
    }
    
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeService()
    {
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->task, $payload = new ReceiveProgramApplicationPayload($this->programId, $this->clientId));
        $this->handle();
    }
}
