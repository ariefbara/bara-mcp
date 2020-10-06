<?php

namespace Participant\Domain\SharedModel;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    SharedModel\ActivityLog\TeamMemberActivityLog
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ActivityLogTest extends TestBase
{
    protected $activityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLog = new TestableActivityLog("id", "message");
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $activityLog = new TestableActivityLog($this->id, $this->message);
        $this->assertEquals($this->id, $activityLog->id);
        $this->assertEquals($this->message, $activityLog->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $activityLog->occuredTime);
    }
    
    public function test_setOperator_setTeamMemberActivityLog()
    {
        $teamMemberActivityLog = new TeamMemberActivityLog($this->activityLog, $this->activityLog->id, $this->teamMember);
        $this->activityLog->setOperator($this->teamMember);
        $this->assertEquals($teamMemberActivityLog, $this->activityLog->teamMemberActivityLog);
    }
}

class TestableActivityLog extends ActivityLog
{
    public $id;
    public $message;
    public $occuredTime;
    public $teamMemberActivityLog;
}
