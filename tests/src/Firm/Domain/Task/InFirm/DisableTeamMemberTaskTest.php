<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\TeamRelatedTaskTestBase;

class DisableTeamMemberTaskTest extends TeamRelatedTaskTestBase
{
    protected $memberId = 'memberId';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $payload = new DisableTeamMemberPayload($this->teamId, $this->memberId);
        $this->task = new DisableTeamMemberTask($this->teamRepository, $payload);
    }
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_executeInFirm_disableMemberInTeam()
    {
        $this->team->expects($this->once())
                ->method('disableMember')
                ->with($this->memberId);
        $this->executeInFirm();
    }
    public function test_executeInFirm_assertTeamManageableInFirm()
    {
        $this->team->expects($this->once())
                ->method('assertManageableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
