<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationSession,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultationSessionActivityLogTest extends TestBase
{
    protected $consultationSession;
    protected $consultationSessionActivityLog;
    protected $activityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionActivityLog = new TestableConsultationSessionActivityLog($this->consultationSession, "id", "message");
        
        $this->activityLog = $this->buildMockOfClass(ActivityLog::class);
        $this->consultationSessionActivityLog->activityLog = $this->activityLog;
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationSessionActivityLog = new TestableConsultationSessionActivityLog($this->consultationSession, $this->id, $this->message);
        $this->assertEquals($this->consultationSession, $consultationSessionActivityLog->consultationSession);
        $this->assertEquals($this->id, $consultationSessionActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message);
        $this->assertEquals($activityLog, $consultationSessionActivityLog->activityLog);
    }
    
    public function test_setOperator_executeActivityLogsSetOperatorMethod()
    {
        $this->activityLog->expects($this->once())
                ->method("setOperator")
                ->with($this->teamMember);
        $this->consultationSessionActivityLog->setOperator($this->teamMember);
    }
}

class TestableConsultationSessionActivityLog extends ConsultationSessionActivityLog
{
    public $consultationSession;
    public $id;
    public $activityLog;
}
