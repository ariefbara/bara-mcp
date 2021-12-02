<?php

namespace Personnel\Domain\Model\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ {
    ConsultationRequest,
    ConsultationSession
};
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $consultationSession;
    protected $participant, $programId = 'programId';
    
    protected $consultationRequest;


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->participant = new TestableParticipant();
        $this->participant->programId = $this->programId;
        
        $this->participant->consultationSessions = new ArrayCollection();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->participant->consultationSessions->add($this->consultationSession);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }
    protected function executeHasConsultationSessionInConflictWithConsultationRequest()
    {
        return $this->participant->hasConsultationSessionInConflictWithConsultationRequest($this->consultationRequest);
    }
    public function test_executeHasConsultationSessionInConflictWithConsultationRequest_returnFalse()
    {
        $this->assertFalse($this->executeHasConsultationSessionInConflictWithConsultationRequest());
    }
    public function test_executeHasConsultationSessionInConflictWithConsultationRequest_hasConsultationSessionConflictedWithConsultationRequest()
    {
        $this->consultationSession->expects($this->once())
            ->method('intersectWithConsultationRequest')
            ->with($this->consultationRequest)
            ->willReturn(true);
        $this->assertTrue($this->executeHasConsultationSessionInConflictWithConsultationRequest());
    }
    
    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->programEquals($this->participant->programId));
    }
    public function test_programEquals_differentProgram_returnFalse()
    {
        $this->assertFalse($this->participant->programEquals("differentProgramId"));
    }
    
    protected function manageableInProgram()
    {
        return $this->participant->manageableInProgram($this->programId);
    }
    public function test_manageableInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->manageableInProgram());
    }
    public function test_manageableInProgram_differentProgram_returnFalse()
    {
        $this->participant->programId = 'differentProgramId';
        $this->assertFalse($this->manageableInProgram());
    }
    public function test_manageableInProgram_inactiveParticipant_returnFalse()
    {
        $this->participant->active = false;
        $this->assertFalse($this->manageableInProgram());
    }
    
    protected function assertUsableInProgram()
    {
        $this->participant->assertUsableInProgram($this->programId);
    }
    public function test_assertUsableInProgram_activeParticipantOfSameProgram_void()
    {
        $this->assertUsableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: can only use active participant in same program');
    }
    public function test_assertUsableInProgram_differentProgram_forbidden()
    {
        $this->participant->programId = 'differentProgram';
        $this->assertRegularExceptionThrowed(function() {
            $this->assertUsableInProgram();
        }, 'Forbidden', 'forbidden: can only use active participant in same program');
    }
}

class TestableParticipant extends Participant
{
    public $programId = "programId", $id, $acceptedTime, $active = true, $note; 
    public $consultationSessions;
    
    public function __construct()
    {
        parent::__construct();
    }
}
