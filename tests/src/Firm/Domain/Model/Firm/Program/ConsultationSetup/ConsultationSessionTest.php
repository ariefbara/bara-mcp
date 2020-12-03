<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Resources\Domain\ValueObject\DateTimeInterval;
use Tests\TestBase;

class ConsultationSessionTest extends TestBase
{
    protected $consultationSession;
    protected $startEndTime;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSession = new TestableConsultationSession();
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->consultationSession->startEndTime = $this->startEndTime;
    }
    
    protected function executeDisableUpcomingSession()
    {
        $this->startEndTime->expects($this->any())
                ->method("isUpcoming")
                ->willReturn(true);
        $this->consultationSession->disableUpcomingSession();
    }
    public function test_disableUpcomingSession_cancelSessionAndSetNote()
    {
        $this->executeDisableUpcomingSession();
        $this->assertTrue($this->consultationSession->cancelled);
        $this->assertEquals("inactive consultant", $this->consultationSession->note);
    }
    public function test_disableUpcomingSession_alreadyCancelled_nop()
    {
        $this->consultationSession->cancelled = true;
        $this->executeDisableUpcomingSession();
        $this->assertNull($this->consultationSession->note);
    }
    public function test_disableUpcomingSession_alreadyPassed_nop()
    {
        $this->startEndTime->expects($this->once())
                ->method("isUpcoming")
                ->willReturn(false);
        $this->executeDisableUpcomingSession();
        $this->assertFalse($this->consultationSession->cancelled);
        $this->assertNull($this->consultationSession->note);
    }
}

class TestableConsultationSession extends ConsultationSession
{
    public $consultationSetup;
    public $id;
    public $consultant;
    public $startEndTime;
    public $cancelled = false;
    public $note;
    
    function __construct()
    {
        parent::__construct();
    }
}
