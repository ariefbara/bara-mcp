<?php

namespace Participant\Domain\DependencyModel\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Program\RegistrationPhase;
use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program;
    protected $participantTypes;
    protected $registrationPhase;
    
    protected $participantType = "team";

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        $this->program->registrationPhases = new ArrayCollection();
        
        $this->participantTypes = $this->buildMockOfClass(ParticipantTypes::class);
        $this->program->participantTypes = $this->participantTypes;
        
        $this->registrationPhase = $this->buildMockOfClass(RegistrationPhase::class);
        $this->program->registrationPhases->add($this->registrationPhase);
    }
    
    public function test_firmIdEquals_sameFirmId_returnTrue()
    {
        $this->assertTrue($this->program->firmIdEquals($this->program->firmId));
    }
    public function test_firmIdEquals_differentFirmId_returnFalse()
    {
        $this->assertFalse($this->program->firmIdEquals("differentId"));
    }
    protected function executeIsRegistrationOpenFor()
    {
        $this->participantTypes->expects($this->any())
                ->method('hasType')
                ->willReturn(true);
        $this->registrationPhase->expects($this->any())
                ->method('isOpen')
                ->willReturn(true);
        return $this->program->isRegistrationOpenFor($this->participantType);
    }
    public function test_isRegistrationOpenFor_returnTrue()
    {
        $this->assertTrue($this->executeIsRegistrationOpenFor());
    }
    public function test_isRegistrationOpenFor_noOpenRegistrationPhase_returnFalses()
    {
        $this->registrationPhase->expects($this->once())
                ->method('isOpen')
                ->willReturn(false);
        $this->assertFalse($this->executeIsRegistrationOpenFor());
    }
    public function test_isRegistrationOpenFor_participantTypeNotIncludedInParticipantTypesList_returnFalse()
    {
        $this->participantTypes->expects($this->once())
                ->method('hasType')
                ->with($this->participantType)
                ->willReturn(false);
        $this->assertFalse($this->executeIsRegistrationOpenFor());
    }
    public function test_isRegistrationOpenFor_programRemoved_returnFalse()
    {
        $this->program->removed = true;
        $this->assertFalse($this->executeIsRegistrationOpenFor());
    }
}

class TestableProgram extends Program
{
    public $firmId = "firmId";
    public $id;
    public $participantTypes;
    public $removed = false;
    public $registrationPhases;
    
    function __construct()
    {
        parent::__construct();
    }
}
