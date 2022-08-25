<?php

namespace Firm\Application\Listener;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\TestBase;

class AcceptProgramApplicationFromClientTest extends TestBase
{

    protected $clientRepository, $client, $clientId = 'clientId';
    protected $programRepository, $program, $programId = 'programId';
    protected $dispatcher;
    protected $listener;
    //
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->client = $this->buildMockOfClass(Client::class);

        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        
        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);
        
        $this->listener = new AcceptProgramApplicationFromClient($this->programRepository, $this->clientRepository, $this->dispatcher);
        
        $this->event = new \Client\Domain\Event\ClientHasAppliedToProgram($this->clientId, $this->programId);
    }
    
    protected function handle()
    {
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);
        
        $this->listener->handle($this->event);
    }
    public function test_handle_programReceiveApplicationFromClient()
    {
        $this->program->expects($this->once())
                ->method('receiveApplication')
                ->with($this->client);
        $this->handle();
    }
    public function test_handle_updateProgramRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->handle();
    }
    public function test_handle_dispatchProgram()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->handle();
    }

}
