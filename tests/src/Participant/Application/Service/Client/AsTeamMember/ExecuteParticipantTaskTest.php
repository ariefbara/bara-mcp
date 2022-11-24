<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Tests\TestBase;

class ExecuteParticipantTaskTest extends TestBase
{
    protected $teamMemberRepository, $teamMember, $firmId = 'firmId', $clientId = 'clientId', $teamId = 'teamId';
    protected $teamParticipantRepository, $teamParticipant, $teamParticipantId = 'teamParticipantId';
    protected $service;
    //
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMember = $this->buildMockOfClass(\Participant\Domain\DependencyModel\Firm\Client\TeamMembership::class);
        
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipant = $this->buildMockOfClass(\Participant\Domain\Model\TeamProgramParticipation::class);
        
        $this->service = new ExecuteParticipantTask($this->teamMemberRepository, $this->teamParticipantRepository);
        //
        $this->task = $this->buildMockOfInterface(\Participant\Domain\Task\Participant\ParticipantTask::class);
    }
    
    //
    protected function execute()
    {
        $this->teamMemberRepository->expects($this->any())
                ->method('aTeamMembershipCorrespondWithTeam')
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
        
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->task, $this->payload);
    }
    public function test_execute_memberExecuteParticipantTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeParticipantTask')
                ->with($this->teamParticipant, $this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
