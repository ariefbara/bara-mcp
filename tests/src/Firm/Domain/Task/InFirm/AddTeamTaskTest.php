<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Model\Firm\Team\MemberData;
use Firm\Domain\Model\Firm\TeamData;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Tests\src\Firm\Domain\Task\InFirm\TeamRelatedTaskTestBase;

class AddTeamTaskTest extends TeamRelatedTaskTestBase
{
    protected $clientRepository;
    protected $client;
    protected $clientId = 'clientId';
    protected $payload, $name = 'new team', $position = 'new position';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientRepository = $this->buildMockOfInterface(ClientRepository::class);
        $this->clientRepository->expects($this->any())
                ->method('ofId')
                ->with($this->clientId)
                ->willReturn($this->client);
        
        $this->payload = new AddTeamPayload($this->name);
        $this->payload->addMemberDataRequest(new MemberDataRequest($this->clientId, $this->position));
        
        $this->task = new AddTeamTask($this->teamRepository, $this->clientRepository, $this->payload);
    }
    
    protected function execute()
    {
        $this->teamRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->teamId);
        $this->task->executeInFirm($this->firm);
    }
    public function test_execute_addTeamCreatedInFirmToRepository()
    {
        $teamData = new TeamData($this->name);
        $teamData->addMemberData(new MemberData($this->client, $this->position));
        
        $this->firm->expects($this->once())
                ->method('createTeam')
                ->with($this->teamId, $teamData)
                ->willReturn($this->team);
        $this->teamRepository->expects($this->once())
                ->method('add')
                ->with($this->team);
        $this->execute();
    }
    public function test_execute_assertClientUsableInFirm()
    {
        $this->client->expects($this->once())
                ->method('assertUsableInFirm');
        $this->execute();
    }
    public function test_execute_setAddedTeamId()
    {
        $this->execute();
        $this->assertSame($this->teamId, $this->task->addedTeamId);
    }
    
}
