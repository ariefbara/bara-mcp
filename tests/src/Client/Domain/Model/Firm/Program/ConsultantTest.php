<?php

namespace Client\Domain\Model\Firm\Program;

use Client\Domain\Model\Client\ProgramParticipation\ {
    ConsultationRequest,
    ConsultationSession
};
use Doctrine\Common\Collections\ArrayCollection;
use Tests\TestBase;

class ConsultantTest extends TestBase
{

    protected $consultant;
    protected $consultationSession;
    protected $consultationRequest, $startEndTime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->consultant = new TestableConsultant();
        $this->consultant->consultationSessions = new ArrayCollection();
        $this->consultant->consultationRequests = new ArrayCollection();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultant->consultationSessions->add($this->consultationSession);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }
    protected function executeHasConsultationSessionConflictedWith()
    {
        return $this->consultant->hasConsultationSessionConflictedWith($this->consultationRequest);
    }
    public function test_hasConsultationSessionConflictedWith_returnFalse()
    {
        $this->assertFalse($this->executeHasConsultationSessionConflictedWith());
    }
    public function test_hasConsultationSessionConflictedWith_containConsultationSessionConflictedWithNegoitateConsultationSessionArg_returnTrue()
    {
        $this->consultationSession->expects($this->once())
            ->method('conflictedWithConsultationRequest')
            ->with($this->consultationRequest)
            ->willReturn(true);
        $this->assertTrue($this->executeHasConsultationSessionConflictedWith());
    }
}

class TestableConsultant extends Consultant
{

    public $program, $id, $removed;
    public $consultationSessions, $consultationRequests;
    
    function __construct()
    {
        parent::__construct();
    }

}
