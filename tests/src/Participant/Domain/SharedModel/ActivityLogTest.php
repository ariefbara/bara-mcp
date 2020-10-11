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
        $this->activityLog = new TestableActivityLog("id", "message", null);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableActivityLog($this->id, $this->message, $this->teamMember);
    }
    
    public function test_construct_setProperties()
    {
        $activityLog = $this->executeConstruct();
        $this->assertEquals($this->id, $activityLog->id);
        $this->assertEquals($this->message, $activityLog->message);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $activityLog->occuredTime);
        
        $teamMemberActivityLog = new TeamMemberActivityLog($activityLog, $this->id, $this->teamMember);
        $this->assertEquals($teamMemberActivityLog, $activityLog->teamMemberActivityLog);
    }
    public function test_construct_emptyTeamMember_setTeamMemberActivityLogNull()
    {
        $this->teamMember = null;
        $activityLog = $this->executeConstruct();
        $this->assertNull($activityLog->teamMemberActivityLog);
    }
    
}

class TestableActivityLog extends ActivityLog
{
    public $id;
    public $message;
    public $occuredTime;
    public $teamMemberActivityLog;
}
