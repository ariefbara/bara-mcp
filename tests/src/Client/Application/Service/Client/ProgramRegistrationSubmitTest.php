<?php

namespace Client\Application\Service\Client;

use Client\ {
    Application\Service\ClientRepository,
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Client,
    Domain\Model\Firm\Program
};
use Tests\TestBase;

class ProgramRegistrationSubmitTest extends TestBase
{
    protected $service;
    protected $programRegistrationRepository;
    protected $programRepository, $program, $programmeCompositionId, $firmId = 'firmId', $programId = 'program-id';
    protected $clientRepository, $client, $clientId = 'client-id';
    protected $nextId = 'id';


    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationRepository = $this->buildMockOfInterface(ProgramRegistrationRepository::class);
        $this->programRegistrationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->programId)
            ->willReturn($this->program);
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
            ->method('ofId')
            ->with($this->clientId)
            ->willReturn($this->client);
        
        $this->service = new ProgramRegistrationSubmit($this->programRegistrationRepository, $this->clientRepository, $this->programRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->clientId, $this->firmId, $this->programId);
    }
    public function test_execute_addProgramRegistrationToRepository()
    {
        $this->programRegistrationRepository->expects($this->once())
            ->method('add');
        $this->execute();
    }
    public function test_execute_createProgramRegistrationInClient()
    {
        $this->client->expects($this->once())
            ->method('createProgramRegistration')
            ->with($this->nextId, $this->program);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
    
}
