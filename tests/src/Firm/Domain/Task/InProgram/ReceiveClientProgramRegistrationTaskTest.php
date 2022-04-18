<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Resources\Application\Event\Dispatcher;
use Tests\src\Firm\Domain\Task\InProgram\TaskInProgramTestBase;

class ReceiveClientProgramRegistrationTaskTest extends TaskInProgramTestBase
{
    protected $task;
    protected $clientRepository, $client, $clientId = 'client-id';
    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        $this->task = new ReceiveClientProgramRegistrationTask($this->clientRepository, $this->clientId, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->task->execute($this->program);
    }
    public function test_execute_programReceiveRegistration()
    {
        $this->program->expects($this->once())
                ->method('receiveRegistrationFromApplicant')
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
}
