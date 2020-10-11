<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\{
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationSession,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultationSessionActivityLogTest extends TestBase
{

    protected $consultationSession;
    protected $consultationSessionActivityLog;
    protected $id = "newId", $message = "new message";
    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationSessionActivityLog = new TestableConsultationSessionActivityLog($this->consultationSession,
                "id", "message", null);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }

    protected function executeConstruct()
    {
        return new TestableConsultationSessionActivityLog(
                $this->consultationSession, $this->id, $this->message, $this->teamMember);
    }

    public function test_construct_setProperties()
    {
        $consultationSessionActivityLog = $this->executeConstruct();
        $this->assertEquals($this->consultationSession, $consultationSessionActivityLog->consultationSession);
        $this->assertEquals($this->id, $consultationSessionActivityLog->id);

        $activityLog = new ActivityLog($this->id, $this->message, $this->teamMember);
        $this->assertEquals($activityLog, $consultationSessionActivityLog->activityLog);
    }

}

class TestableConsultationSessionActivityLog extends ConsultationSessionActivityLog
{

    public $consultationSession;
    public $id;
    public $activityLog;

}
