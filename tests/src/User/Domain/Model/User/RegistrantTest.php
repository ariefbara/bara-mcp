<?php

namespace User\Domain\Model\User;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;
use User\Domain\Model\ProgramInterface;

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
}

class TestableRegistrant extends Registrant
{
    public $programId;
    public $id;
    public $concluded;
    public $registeredTime;
    public $note;
}
