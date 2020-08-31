<?php

namespace SharedContext\Domain\Model\SharedEntity;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class RegistrantTest extends TestBase
{
    protected $registrant;
    protected $id = 'newRegistrantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = new TestableRegistrant('id');
        $this->registrant->concluded = false;
    }
    
    public function test_construct_setProperties()
    {
        $registrant = new TestableRegistrant($this->id);
        $this->assertEquals($this->id, $registrant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $registrant->registeredTime);
        $this->assertFalse($registrant->concluded);
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
    public $id;
    public $concluded;
    public $registeredTime;
    public $note;
    
}
