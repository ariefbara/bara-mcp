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
    protected $participant;
    
    protected $consultationRequest;


    protected function setUp(): void
    {
        parent::setUp();
        
        $this->participant = new TestableParticipant();
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
}

class TestableParticipant extends Participant
{
    public $programId = "programId", $id, $acceptedTime, $active, $note; 
    public $consultationSessions;
    
    public function __construct()
    {
        parent::__construct();
    }
}
