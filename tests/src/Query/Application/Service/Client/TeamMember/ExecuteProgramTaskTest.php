<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByParticipant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Tests\src\Query\Application\Service\Client\TeamMember\TeamMemberTaskBase;

class ExecuteProgramTaskTest extends TeamMemberTaskBase
{
    protected $teamParticipantRepository;
    protected $teamParticipant;
    protected $teamParticipantId = 'teamParticipantId';
    protected $service;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->teamParticipant = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->teamParticipantRepository = $this->buildMockOfInterface(TeamParticipantRepository::class);
        $this->teamParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->teamParticipantId)
                ->willReturn($this->teamParticipant);
        
        $this->service = new ExecuteProgramTask($this->teamMemberRepository, $this->teamParticipantRepository);
        
        $this->task = $this->buildMockOfInterface(ITaskInProgramExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->task);
    }
    public function test_execute_memberExecuteProgramTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeProgramTask')
                ->with($this->teamParticipant, $this->task);
        $this->execute();
    }
}
