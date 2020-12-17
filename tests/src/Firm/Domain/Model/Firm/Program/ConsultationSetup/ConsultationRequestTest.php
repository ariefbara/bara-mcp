<?php

namespace Firm\Domain\Model\Firm\Program\ConsultationSetup;

use Resources\Domain\ValueObject\DateTimeInterval;
use Tests\TestBase;

class ConsultationRequestTest extends TestBase
{
    protected $consultationRequest;
    protected $startEndTime;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequest = new TestableConsultationRequest();
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->consultationRequest->startEndTime = $this->startEndTime;
    }
    
    protected function executeDisableUpcomingRequest()
    {
        $this->startEndTime->expects($this->any())
                ->method("isUpcoming")
                ->willReturn(true);
        $this->consultationRequest->disableUpcomingRequest();
    }
    public function test_disableUpcomingRequest_concludeRequestAndSetNote()
    {
        $this->executeDisableUpcomingRequest();
        $this->assertTrue($this->consultationRequest->concluded);
        $this->assertEquals("disabled by system", $this->consultationRequest->status);
    }
    public function test_disableUpcomingRequest_alreadyConcluded_nop()
    {
        $this->consultationRequest->concluded = true;
        $this->executeDisableUpcomingRequest();
        $this->assertNull($this->consultationRequest->status);
    }
    public function test_disableUpcomingRequest_notAnUpcomingRequest_nop()
    {
        $this->startEndTime->expects($this->once())
                ->method("isUpcoming")
                ->willReturn(false);
        $this->executeDisableUpcomingRequest();
        $this->assertFalse($this->consultationRequest->concluded);
        $this->assertNull($this->consultationRequest->status);
    }
}

class TestableConsultationRequest extends ConsultationRequest
{
    public $consultationSetup;
    public $id;
    public $consultant;
    public $startEndTime;
    public $concluded = false;
    public $status;
    
    function __construct()
    {
        parent::__construct();
    }
}
