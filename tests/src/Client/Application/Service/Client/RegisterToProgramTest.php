<?php

namespace Client\Application\Service\Client;

use Client\ {
    Application\Service\ClientRepository,
    Domain\Model\Client,
    Domain\Model\Client\ProgramRegistration
};
use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\Firm\Program;
use Tests\TestBase;

class RegisterToProgramTest extends TestBase
{

    protected $service;
    protected $programRegistrationRepository, $nextId = 'nextId';
    protected $clientRepository, $client;
    protected $programRepository, $program;
    protected $dispatcher;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationRepository = $this->buildMockOfInterface(ProgramRegistrationRepository::class);
        $this->programRegistrationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new RegisterToProgram(
                $this->programRegistrationRepository, $this->clientRepository, $this->programRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->programId);
    }

    public function test_execute_addProgramRegistrationToRepository()
    {
        $this->client->expects($this->once())
                ->method('registerToProgram')
                ->with($this->nextId, $this->program)
                ->willReturn($programRegistration = $this->buildMockOfClass(ProgramRegistration::class));
        
        $this->programRegistrationRepository->expects($this->once())
                ->method('add')
                ->with($programRegistration);
        
        $this->execute();
    }
    public function test_execute_dispatchClientToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->client);
        $this->execute();
    }
    
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
