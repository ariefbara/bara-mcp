<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\ProgramInterface;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class RegistrantTest extends TestBase
{
    protected $registrant;
    protected $program, $programId = 'programId';
    
    protected $id = 'registrantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfInterface(ProgramInterface::class);
        $this->program->expects($this->any())->method('getId')->willReturn($this->programId);
        
        $this->registrant = new TestableRegistrant($this->program, 'id');
    }
    
    public function test_construct_setProperties()
    {
        $registrant = new TestableRegistrant($this->program, $this->id);
        $this->assertEquals($this->programId, $registrant->programId);
        $this->assertEquals($this->id, $registrant->id);
        $this->assertFalse($registrant->concluded);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $registrant->registeredTime);
        $this->assertNull($registrant->note);
    }
    
    protected function executeCancel()
    {
        $this->registrant->cancel();
    }
    public function test_cancel_setConcludedTrueAndNoteCancelled()
    {
        $this->executeCancel();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('cancelled', $this->registrant->note);
    }
    public function test_cancel_alreadyConcluded_forbiddenError()
    {
        $this->registrant->concluded = true;
        
        $operation = function (){
            $this->executeCancel();
        };
        $errorDetail = 'forbidden: program registration already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeIsUnconcludedRegistrationToProgram()
    {
        return $this->registrant->isUnconcludedRegistrationToProgram($this->program);
    }
    public function test_isUnconcludedRegistrationToProgram_returnTrue()
    {
        $this->assertTrue($this->executeIsUnconcludedRegistrationToProgram());
    }
    public function test_isUnconcludedRegistrationToProgram_differentProgramId_returnFalse()
    {
        $this->registrant->programId = 'differentProgramId';
        $this->assertFalse($this->executeIsUnconcludedRegistrationToProgram());
    }
    public function test_isUnconcludedRegistrationToProgram_concludedRegistrant_returnFalse()
    {
        $this->registrant->concluded = true;
        $this->assertFalse($this->executeIsUnconcludedRegistrationToProgram());
    }
}

class TestableRegistrant extends Registrant
{
    public $programId;
    public $id;
    public $concluded;
    public $registeredTime;
    public $note;
}
