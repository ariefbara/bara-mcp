<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\Worksheet,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class WorksheetActivityLogTest extends TestBase
{
    protected $worksheet;
    protected $worksheetActivityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetActivityLog = new TestableWorksheetActivityLog($this->worksheet, $this->id, $this->message, null);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $worksheetActivityLog = new TestableWorksheetActivityLog($this->worksheet, $this->id, $this->message, $this->teamMember);
        $this->assertEquals($this->worksheet, $worksheetActivityLog->worksheet);
        $this->assertEquals($this->id, $worksheetActivityLog->id);
        $activityLog = new ActivityLog($this->id, $this->message, $this->teamMember);
        $this->assertEquals($activityLog, $worksheetActivityLog->activityLog);
    }
    
}

class TestableWorksheetActivityLog extends WorksheetActivityLog
{
    public $worksheet;
    public $id;
    public $activityLog;
}
