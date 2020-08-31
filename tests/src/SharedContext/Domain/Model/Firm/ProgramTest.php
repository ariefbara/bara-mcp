<?php

namespace SharedContext\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\ParticipantTypes;
use SharedContext\Domain\Model\Firm\Program\RegistrationPhase;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $program, $participantTypes, $firmId = 'firmId', $registrationPhase;
    
    protected $participantType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = new TestableProgram();
        $this->program->firmId = $this->firmId;
        $this->participantTypes = $this->buildMockOfClass(ParticipantTypes::class);
        $this->program->participantTypes = $this->participantTypes;
        
        $this->program->registrationPhases = new ArrayCollection();
        $this->registrationPhase = $this->buildMockOfClass(RegistrationPhase::class);
        $this->program->registrationPhases->add($this->registrationPhase);
        
        $this->participantType = ParticipantTypes::CLIENT_TYPE;
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
    
    protected function executeFirmIdEquals()
    {
        return $this->program->firmIdEquals($this->firmId);
    }
    public function test_firmIdEquals_sameFirmId_returnTrue()
    {
        $this->assertTrue($this->executeFirmIdEquals());
    }
    public function test_firmIdEquals_differentFirmId_returnFalse()
    {
        $this->program->firmId = 'different';
        $this->assertFalse($this->executeFirmIdEquals());
    }
    
}

class TestableProgram extends Program
{
    public $firmId;
    public $id;
    public $participantTypes;
    public $removed = false;
    public $registrationPhases;
    
    function __construct()
    {
    }
}
