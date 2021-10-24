<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Tests\src\Participant\Application\Service\Client\AsTeamMember\TeamMemberBaseTest;

class ExecuteTeamParticipantTaskTest extends TeamMemberBaseTest
{
    protected $service;
    protected $task;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ExecuteTeamParticipantTask($this->teamMemberRepository, $this->teamParticipantRepository);
        $this->task = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamId, $this->teamParticipantId, $this->task);
    }
    public function test_execute_teamMemberExecuteTeamParticipantTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeTeamParticipantTask')
                ->with($this->teamParticipant, $this->task);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
