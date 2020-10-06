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
    protected $activityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetActivityLog = new TestableWorksheetActivityLog($this->worksheet, $this->id, $this->message);
        
        $this->activityLog = $this->buildMockOfClass(ActivityLog::class);
        $this->worksheetActivityLog->activityLog = $this->activityLog;
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $worksheetActivityLog = new TestableWorksheetActivityLog($this->worksheet, $this->id, $this->message);
        $this->assertEquals($this->worksheet, $worksheetActivityLog->worksheet);
        $this->assertEquals($this->id, $worksheetActivityLog->id);
        $activityLog = new ActivityLog($this->id, $this->message);
        $this->assertEquals($activityLog, $worksheetActivityLog->activityLog);
    }
    
    public function test_setOperator_executeActivityLogSetOperator()
    {
        $this->activityLog->expects($this->once())
                ->method("setOperator")
                ->with($this->teamMember);
        $this->worksheetActivityLog->setOperator($this->teamMember);
    }
}

class TestableWorksheetActivityLog extends WorksheetActivityLog
{
    public $worksheet;
    public $id;
    public $activityLog;
}
