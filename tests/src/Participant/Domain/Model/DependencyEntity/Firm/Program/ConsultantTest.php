<?php

namespace Participant\Domain\Model\DependencyEntity\Firm\Program;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant\ {
    ConsultationRequest,
    ConsultationSession
};
use SharedContext\Domain\Model\Firm\Program;
use Tests\TestBase;

class ConsultantTest extends TestBase
{
    protected $consultant;
    protected $programId = 'programId';
    protected $consultationSession;
    
    protected $consultationRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultant = new TestableConsultant(); 
        $this->consultant->programId = $this->programId;
        $this->consultant->consultationSessions = new ArrayCollection();
        
        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultant->consultationSessions->add($this->consultationSession);
        
        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
    }
    
    protected function executeProgramIdEquals()
    {
        return $this->consultant->programIdEquals($this->programId);
    }
    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->executeProgramIdEquals());
    }
    public function test_programEquals_differentProgram_returnFalse()
    {
        $this->consultant->programId = 'different';
        $this->assertFalse($this->executeProgramIdEquals());
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
    public $programId;
    public $id;
    public $personnelId;
    public $removed;
    public $consultationSessions;
    
    function __construct()
    {
        parent::__construct();
    }
}
