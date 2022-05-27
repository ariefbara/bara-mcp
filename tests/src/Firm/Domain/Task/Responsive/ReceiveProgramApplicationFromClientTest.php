<?php

namespace Firm\Domain\Task\Responsive;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;
use Resources\Application\Event\AdvanceDispatcher;
use Tests\TestBase;

class ReceiveProgramApplicationFromClientTest extends TestBase
{

    protected $programRepository, $program, $programId = 'program-id';
    protected $clientRepository, $client, $clientId = 'client-id';
    protected $dispatcher;
    protected $service;
    protected $payload;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('aProgramOfId')
                ->with($this->programId)
                ->willReturn($this->program);

        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);

        $this->dispatcher = $this->buildMockOfClass(AdvanceDispatcher::class);

        $this->service = new ReceiveProgramApplicationFromClient($this->programRepository, $this->clientRepository,
                $this->dispatcher);
        
        $this->payload = new ReceiveProgramApplicationPayload($this->programId, $this->clientId);
    }
    
    protected function execute()
    {
        $this->service->execute($this->payload);
    }
    public function test_execute_programReceiveApplication()
    {
        $this->program->expects($this->once())
                ->method('receiveApplication')
                ->with($this->client);
        $this->execute();
    }
    public function test_execute_dispatcheProgram()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->execute();
    }

}
