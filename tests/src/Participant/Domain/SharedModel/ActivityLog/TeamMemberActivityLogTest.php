<?php

namespace Participant\Domain\SharedModel\ActivityLog;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class TeamMemberActivityLogTest extends TestBase
{
    protected $activityLog;
    protected $teamMember;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLog = $this->buildMockOfClass(ActivityLog::class);
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $teamMemberActivityLog = new TestableTeamMemberActivityLog($this->activityLog, $this->id, $this->teamMember);
        $this->assertEquals($this->activityLog, $teamMemberActivityLog->activityLog);
        $this->assertEquals($this->id, $teamMemberActivityLog->id);
        $this->assertEquals($this->teamMember, $teamMemberActivityLog->teamMember);
    }
}

class TestableTeamMemberActivityLog extends TeamMemberActivityLog
{
    public $activityLog;
    public $id;
    public $teamMember;
}
