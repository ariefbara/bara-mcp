<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\ {
    Model\Firm\Personnel\ProgramConsultant,
    Model\Firm\Personnel\ProgramConsultant\ConsultationSession,
    SharedModel\ActivityLog
};
use Tests\TestBase;

class ConsultationSessionActivityLogTest extends TestBase
{
    protected $consultationSession;
    protected $consultant;
    protected $id = "newId", $message = "new message";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultant = $this->buildMockOfClass(ProgramConsultant::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableConsultationSessionActivityLog($this->consultationSession, $this->id, $this->message, $this->consultant);
    }
    
    public function test_construct_setPropert_expectedResult()
    {
        $consultationSessionActivityLog = $this->executeConstruct();
        $this->assertEquals($this->consultationSession, $consultationSessionActivityLog->consultationSession);
        $this->assertEquals($this->id, $consultationSessionActivityLog->id);
        
        $activityLog = new ActivityLog($this->id, $this->message, $this->consultant);
        $this->assertEquals($activityLog, $consultationSessionActivityLog->activityLog);
    }
}

class TestableConsultationSessionActivityLog extends ConsultationSessionActivityLog
{
    public $consultationSession;
    public $id;
    public $activityLog;
}
