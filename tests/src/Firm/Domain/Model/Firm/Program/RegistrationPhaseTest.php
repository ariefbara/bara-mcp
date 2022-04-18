<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateInterval
};
use Tests\TestBase;

class RegistrationPhaseTest extends TestBase
{
    protected $program;
    protected $registrationPhase, $dateInterval;
    protected $id = 'registration-phase-id', $name = 'new registration phase name', $startDate, $endDate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $registrationPhaseData = new RegistrationPhaseData('name', null, null);
        $this->registrationPhase = new TestableRegistrationPhase($this->program, 'id', $registrationPhaseData);
        $this->dateInterval = $this->buildMockOfClass(DateInterval::class);
        $this->registrationPhase->startEndDate = $this->dateInterval;
        
        $this->startDate = new DateTimeImmutable("+7 days");
        $this->endDate = new DateTimeImmutable("+31 days");
    }
    
    protected function getRegistrationPhaseData()
    {
        return new RegistrationPhaseData($this->name, $this->startDate, $this->endDate);
    }
    
    protected function executeConstruct()
    {
        return new TestableRegistrationPhase($this->program, $this->id, $this->getRegistrationPhaseData());
    }
    
    public function test_construct_setProperties()
    {
        $registrationPhase = $this->executeConstruct();
        $this->assertEquals($this->program, $registrationPhase->program);
        $this->assertEquals($this->id, $registrationPhase->id);
        $this->assertEquals($this->name, $registrationPhase->name);
        $startEndDate = new DateInterval($this->startDate, $this->endDate);
        $this->assertEquals($startEndDate, $registrationPhase->startEndDate);
        $this->assertFalse($registrationPhase->removed);
    }
    public function test_construct_emtpyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad requst: registration phase name required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_emptyStartDate_setStartDateOfStartDateNull()
    {
        $this->startDate = null;
        $registrationPhase = $this->executeConstruct();
        $startEndDate = new DateInterval(null, $this->endDate);
        $this->assertEquals($startEndDate, $registrationPhase->startEndDate);
    }
    public function test_construct_emptyEndDate_setEndDateOfStartEndDateNull()
    {
        $this->endDate = null;
        $registrationPhase = $this->executeConstruct();
        $startEndDate = new DateInterval($this->startDate, null);
        $this->assertEquals($startEndDate, $registrationPhase->startEndDate);
    }
    
    protected function executeUpdate()
    {
        $this->registrationPhase->update($this->getRegistrationPhaseData());
    }
    public function test_update_changeProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->registrationPhase->name);
        $startEndDate = new DateInterval($this->startDate, $this->endDate);
        $this->assertEquals($startEndDate, $this->registrationPhase->startEndDate);
    }
    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad requst: registration phase name required";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_update_emptyStartDate_setStartDateOfStartEndDateNull()
    {
        $this->startDate = null;
        $this->executeUpdate();
        $startEndDate = new DateInterval(null, $this->endDate);
        $this->assertEquals($startEndDate, $this->registrationPhase->startEndDate);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->registrationPhase->remove();
        $this->assertTrue($this->registrationPhase->removed);
    }
    
    public function test_isOpen_returnStartEndDateContainCurrentTimeResult()
    {
        $this->dateInterval->expects($this->once())
                ->method('contain')
                ->with(DateTimeImmutableBuilder::buildYmdHisAccuracy())
                ->willReturn(true);
        $this->assertTrue($this->registrationPhase->isOpen());
    }
    public function test_isOpen_alreadyRemoved_returnFalse()
    {
        $this->registrationPhase->removed = true;
        $this->dateInterval->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->assertFalse($this->registrationPhase->isOpen());
    }
    
}

class TestableRegistrationPhase extends RegistrationPhase
{
    public $program, $id, $name, $startEndDate, $removed;
}
