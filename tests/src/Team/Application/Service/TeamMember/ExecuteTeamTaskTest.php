<?php

namespace Team\Application\Service\TeamMember;

use Team\Domain\Model\Team\Member;
use Team\Domain\Task\TeamTask;
use Tests\TestBase;

class ExecuteTeamTaskTest extends TestBase
{
    protected $teamMemberRepository;
    protected $teamMember;
    protected $firmId = 'firmId', $clientId = 'clientId', $teamid = 'teamId';
    protected $service;
    
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMemberRepository = $this->buildMockOfInterface(TeamMemberRepository::class);
        $this->teamMember = $this->buildMockOfClass(Member::class);
        $this->teamMemberRepository->expects($this->any())
                ->method('aMemberCorrespondWithClient')
                ->with($this->firmId, $this->teamid, $this->clientId)
                ->willReturn($this->teamMember);
        
        $this->service = new ExecuteTeamTask($this->teamMemberRepository);
        
        $this->task = $this->buildMockOfInterface(TeamTask::class);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->teamid, $this->task, $this->payload);
    }
    public function test_execute_teamMemberExecuteTeamTask()
    {
        $this->teamMember->expects($this->once())
                ->method('executeTeamTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->teamMemberRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
