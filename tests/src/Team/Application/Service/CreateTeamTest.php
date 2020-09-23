<?php

namespace Team\Application\Service;

use Team\Domain\DependencyModel\Firm\Client;
use Tests\TestBase;

class CreateTeamTest extends TestBase
{
    protected $service;
    protected $teamRepository, $nextId = "nextId";
    protected $clientRepository, $client;
    
    protected $firmId = "firmId", $clientId = "clientId", $name = "team name", $position = "creator position";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->buildMockOfInterface(TeamRepository::class);
        $this->teamRepository->expects($this->any())
                ->method("nextIdentity")
                ->willReturn($this->nextId);
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method("ofId")
                ->with($this->firmId, $this->clientId)
                ->willReturn($this->client);
        
        $this->service = new CreateTeam($this->teamRepository, $this->clientRepository);
    }
    
    protected function execute()
    {
        $this->teamRepository->expects($this->any())
                ->method("isNameAvailable")
                ->willReturn(true);
        return $this->service->execute($this->firmId, $this->clientId, $this->name, $this->position);
    }
    public function test_execute_addTeamToRepository()
    {
        $this->client->expects($this->once())
                ->method("createTeam")
                ->with($this->nextId, $this->name, $this->position);
        $this->teamRepository->expects($this->once())
                ->method("add");
        $this->execute();
    }
    public function test_execute_teamNameUnavailable_conflictError()
    {
        $this->teamRepository->expects($this->once())
                ->method("isNameAvailable")
                ->with($this->firmId, $this->name)
                ->willReturn(false);
        $operation = function (){
            $this->execute();
        };
        $errorDetail = "conflict: team name already registered";
        $this->assertRegularExceptionThrowed($operation, "Conflict", $errorDetail);
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
