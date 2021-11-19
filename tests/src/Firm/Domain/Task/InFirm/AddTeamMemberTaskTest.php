<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Tests\src\Firm\Domain\Task\InFirm\TeamRelatedTaskTestBase;

class AddTeamMemberTaskTest extends TeamRelatedTaskTestBase
{
    protected $clientRepository;
    protected $client;
    protected $clientId = 'clientId';
    protected $position = 'new position';
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
        $payload = new AddTeamMemberPayload($this->teamId, new MemberDataRequest($this->clientId, $this->position));
        $this->task = new AddTeamMemberTask($this->teamRepository, $this->clientRepository, $payload);
    }
    
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_addTeamMember()
    {
        $memberData = new \Firm\Domain\Model\Firm\Team\MemberData($this->client, $this->position);
        $this->team->expects($this->once())
                ->method('addMember')
                ->with($memberData);
        $this->executeInFirm();
    }
    public function test_executeInFirm_setAddedMemberIdFromAddingMemberInTeam()
    {
        $this->team->expects($this->once())
                ->method('addMember')
                ->willReturn($memberId = 'memberId');
        $this->executeInFirm();
        $this->assertEquals($memberId, $this->task->addedMemberId);
        
    }
    public function test_executeInFirm_assertTeamManageableInFirm()
    {
        $this->team->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    public function test_executeInFirm_assertClientUsableInFirm()
    {
        $this->client->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
