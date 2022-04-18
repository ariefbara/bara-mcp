<?php

namespace Firm\Domain\Task\InProgram;

use Config\EventList;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Domain\Event\CommonEvent;
use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class ReceiveApplicationFromClientTest extends TaskInProgramTestBase
{
    protected $clientRepository, $client, $clientId = 'client-id';
    protected $dispatcher;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        $this->task = new ReceiveApplicationFromClient($this->clientRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program, $this->clientId);
    }
    public function test_execute_programReceiveApplication()
    {
        $this->program->expects($this->once())
                ->method('receiveApplication')
                ->with($this->client);
        $this->execute();
    }
    public function test_execute_dispatchProgram()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->execute();
    }
    public function test_execute_dispatchApplicationReceivedCommonEvent()
    {
        $event = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $this->clientId);
        $this->dispatcher->expects($this->once())
                ->method('dispatchEvent')
                ->with($event);
        $this->execute();
    }
}
