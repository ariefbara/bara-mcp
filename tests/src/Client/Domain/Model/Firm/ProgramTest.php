<?php

namespace Client\Domain\Model\Firm;

use Client\Domain\Model\Firm\Program\RegistrationPhase;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $registrationPhase;


    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        $this->program->published = true;
        $this->program->registrationPhases = new ArrayCollection();
        
        $this->registrationPhase = $this->buildMockOfClass(RegistrationPhase::class);
        $this->program->registrationPhases->add($this->registrationPhase);
    }
    
    protected function executeCanAcceptRegistration()
    {
        $this->registrationPhase->expects($this->any())
                ->method('isOpen')
                ->willReturn(true);
        return $this->program->canAcceptRegistration();
    }
    public function test_canAcceptRegistration_returnTrue()
    {
        $this->assertTrue($this->executeCanAcceptRegistration());
    }
    public function test_canAcceptRegistration_programUnpublish_returnFalse()
    {
        $this->program->published = false;
        $this->assertFalse($this->executeCanAcceptRegistration());
    }
    public function test_canAcceptRegistration_containNoOpenRegistrationPhase_returnFalse()
    {
        $this->registrationPhase->expects($this->once())
            ->method('isOpen')
            ->willReturn(false);
        $this->assertFalse($this->executeCanAcceptRegistration());
    }
    public function test_canAcceptRegistration_openRegistrationPhaseInCollectionAlreadyRemoved_returnFalse()
    {
        $this->registrationPhase->expects($this->once())
            ->method('isRemoved')
            ->willReturn(true);
        $this->registrationPhase->expects($this->any())
            ->method('isOpen')
            ->willReturn(true);
        $this->assertFalse($this->executeCanAcceptRegistration());
    }
}

class TestableProgram extends Program
{
    public $firm, $id, $name, $description, $startEndDate, $published, $removed;
    public $registrationPhases;
    
    function __construct()
    {
        parent::__construct();
    }
}
