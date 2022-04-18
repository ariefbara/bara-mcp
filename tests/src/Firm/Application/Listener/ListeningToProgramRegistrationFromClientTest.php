<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\Firm\Program\ExecuteTask;
use Firm\Domain\Task\InProgram\ReceiveApplicationFromClient;
use Tests\TestBase;

class ListeningToProgramRegistrationFromClientTest extends TestBase
{
    protected $service;
    protected $task;
    protected $listener;
    protected $event, $clientId = 'client-id', $programId = 'program-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteTask::class);
        $this->task = $this->buildMockOfClass(ReceiveApplicationFromClient::class);
        $this->listener = new ListeningToProgramRegistrationFromClient($this->service, $this->task);
        
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
                ->with($this->programId, $this->task, $this->clientId);
        $this->handle();
    }
}
