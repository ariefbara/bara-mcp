<?php

namespace Firm\Application\Listener;

use Client\Domain\Event\ClientHasAppliedToProgram;
use Firm\Application\Service\FirmRepository;
use Firm\Domain\Model\Firm;
use Tests\TestBase;

class ListeningToProgramRegistrationFromClientTest extends TestBase
{
    protected $firmRepository, $firm, $firmId = 'firmId';
    protected $task;
    protected $listener;
    protected $event, $clientId = 'client-id', $programId = 'program-id';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firmRepository = $this->buildMockOfInterface(FirmRepository::class);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->firmRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId)
                ->willReturn($this->firm);
        $this->task = $this->buildMockOfClass(\Firm\Domain\Task\InFirm\AcceptProgramApplicationFromClient::class);
        $this->listener = new ListeningToProgramRegistrationFromClient($this->firmRepository, $this->task);
        
        $this->event = new ClientHasAppliedToProgram($this->firmId, $this->clientId, $this->programId);
    }
    
    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_executeTask()
    {
        $payload = new \Firm\Domain\Task\InFirm\AcceptProgramApplicationFromClientPayload($this->programId, $this->clientId);
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->firm, $payload);
        $this->handle();
    }
    public function test_handle_updateRepository()
    {
        $this->firmRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
}
