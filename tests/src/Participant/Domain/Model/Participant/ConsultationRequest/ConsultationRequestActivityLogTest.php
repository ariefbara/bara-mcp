<?php

namespace Participant\Domain\Model\Participant\ConsultationRequest;

use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    Model\Participant\ConsultationRequest,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultationRequestActivityLogTest extends TestBase
{
    protected $consultationRequest;
    protected $consultationRequestActivityLog;
    
    protected $id = "newid", $message = "new activity log message";

    protected $teamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequestActivityLog = new TestableConsultationRequestActivityLog($this->consultationRequest, 'id', 'message', null);
        
        $this->teamMember = $this->buildMockOfClass(TeamMembership::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationRequestActivityLog = new TestableConsultationRequestActivityLog($this->consultationRequest, $this->id, $this->message, $this->teamMember);
        $this->assertEquals($this->consultationRequest, $consultationRequestActivityLog->consultationRequest);
        $this->assertEquals($this->id, $consultationRequestActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message, $this->teamMember);
        $this->assertEquals($activityLog, $consultationRequestActivityLog->activityLog);
    }
}

class TestableConsultationRequestActivityLog extends ConsultationRequestActivityLog
{
    public $consultationRequest;
    public $id;
    public $activityLog;
}
