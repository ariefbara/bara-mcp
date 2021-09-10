<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Tests\src\Query\Application\Service\Client\TeamMember\TeamMemberTaskBase;

class ExecuteParticipantTaskTest extends TeamMemberTaskBase
{
    protected $teamParticipantRepository;
    protected $service;
    
    protected $teamParticipant;
    protected $teamParticipantId = 'teamParticipantId';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->service = new ExecuteParticipantTask($this->teamMemberRepository, $this->teamParticipantRepository);
        
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
        
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->task);
    }
    public function test_execute_teamMemberExecuteParticipantTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeTeamParticipantTask')
                ->with($this->teamParticipant, $this->task);
        $this->execute();
    }
}
