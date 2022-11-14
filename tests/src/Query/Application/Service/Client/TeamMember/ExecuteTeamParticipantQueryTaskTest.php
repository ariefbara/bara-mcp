<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Task\Participant\ParticipantQueryTask;
use Tests\TestBase;

class ExecuteTeamParticipantQueryTaskTest extends TestBase
{
    protected $teamMemberRepository, $teamMember, $firmId = 'firmId', $clientId = 'clientId', $teamId = 'teamId';
    protected $teamParticipantRepository, $teamParticipant, $teamParticipantId = 'teamParticipantId';
    //
    protected $service;
    //
    protected $participantQueryTask, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMember = $this->buildMockOfClass(Member::class);
        
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        
        $this->service = new ExecuteTeamParticipantQueryTask($this->teamMemberRepository, $this->teamParticipantRepository);
        //
        $this->participantQueryTask = $this->buildMockOfInterface(ParticipantQueryTask::class);
    }
    
    protected function execute()
    {
        $this->teamMemberRepository->expects($this->any())
                ->method('aTeamMemberOfClientCorrespondWithTeam')
                ->with($this->firmId, $this->clientId, $this->teamId)
                ->willReturn($this->teamMember);
        
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
        
        $this->service->execute(
                $this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->participantQueryTask, $this->payload);
    }
    public function test_execute_memberExecuteTeamParticipantQueryTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeParticipantQueryTask')
                ->with($this->teamParticipant, $this->participantQueryTask, $this->payload);
        $this->execute();
    }
}
