<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession
};
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $program;
    protected $consultationSession;
    
    protected $consultationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant(); 
        $this->program = $this->buildMockOfClass(Program::class);
        $this->consultant->program = $this->program;
        $this->consultant->consultationSessions = new ArrayCollection();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultant->consultationSessions->add($this->consultationSession);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }
    
    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultant->programEquals($this->program));
    }
    public function test_programEquals_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultant->programEquals($program));
    }
    
    protected function executeCanAcceptConsultationRequest()
    {
        return $this->consultant->canAcceptConsultationRequest($this->consultationRequest);
    }
    public function test_canAcceptConsultationRequest_inactiveConsultant_returnFalse()
    {
        $this->consultant->active = false;
        $this->assertFalse($this->executeCanAcceptConsultationRequest());
    }
    public function test_canAcceptConsultationRequest_hasConsultationSessionInConflictWithRequestedConsultation_returnFalse()
    {
        $this->consultationSession->expects($this->once())
            ->method('conflictedWithConsultationRequest')
            ->with($this->consultationRequest)
            ->willReturn(true);
        $this->assertFalse($this->executeCanAcceptConsultationRequest());
    }
    
    protected function assertUsableInProgram()
    {
        $this->consultant->assertUsableInProgram($this->program);
    }
    public function test_assertUsableInProgram_usableConsultant_void()
    {
        $this->assertUsableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_inactiveConsultant_forbidden()
    {
        $this->consultant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable mentor, either inactive or belongs to different program');
    }
    public function test_assertUsableInProgram_belongsToDifferentProgram_forbidden()
    {
        $this->consultant->program = $this->buildMockOfClass(Program::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: unuseable mentor, either inactive or belongs to different program');
    }
}

class TestableConsultant extends Consultant
{
    public $program;
    public $id;
    public $personnelId;
    public $active = true;
    public $consultationSessions;
    
    function __construct()
    {
        parent::__construct();
    }
}
