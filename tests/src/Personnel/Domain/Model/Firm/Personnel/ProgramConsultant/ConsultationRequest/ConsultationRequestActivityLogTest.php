<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultationRequestActivityLogTest extends TestBase
{

    protected $consultationRequest;
    protected $consultant;
    protected $id = "newId", $message = "new message";

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
    }

    public function test_construct_setProperties()
    {
        $consultationRequestActivityLog = new TestableConsultationRequestActivityLog(
                $this->consultationRequest, $this->id, $this->message, $this->consultant);
        
        $this->assertEquals($this->consultationRequest, $consultationRequestActivityLog->consultationRequest);
        $this->assertEquals($this->id, $consultationRequestActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message, $this->consultant);
        $this->assertEquals($activityLog, $consultationRequestActivityLog->activityLog);
    }

}

class TestableConsultationRequestActivityLog extends ConsultationRequestActivityLog
{

    public $consultationRequest;
    public $id;
    public $activityLog;

}
