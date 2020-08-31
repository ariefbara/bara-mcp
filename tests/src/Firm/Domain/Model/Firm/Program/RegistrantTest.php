<?php

namespace Firm\Domain\Model\Firm\Program;

use Tests\TestBase;

class RegistrantTest extends TestBase
{

    protected $registrant;
    
    protected $id = 'newRegistrantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = new TestableRegistrant('id');
    }
    
    public function test_construct_setProperties()
    {
        $registrant = new TestableRegistrant($this->id);
        $this->assertEquals($this->id, $registrant->id);
        $this->assertEquals(\Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy(), $registrant->registeredTime);
        $this->assertFalse($registrant->concluded);
        $this->assertEmpty($registrant->note);
    }
    protected function executeAccept()
    {
        $this->registrant->accept();
    }

    public function test_accept_setConcludedTrueAndNoteAccepted()
    {
        $this->executeAccept();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('accepted', $this->registrant->note);
    }

    public function test_accept_alreadyConcluded_forbiddenError()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeAccept();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeReject()
    {
        $this->registrant->reject();
    }

    public function test_reject_setConcludedFlagTrueAndNoteRejected()
    {
        $this->executeReject();
        $this->assertTrue($this->registrant->concluded);
        $this->assertEquals('rejected', $this->registrant->note);
    }
    public function test_reject_alreadyConcluded_throwEx()
    {
        $this->registrant->concluded = true;
        $operation = function () {
            $this->executeReject();
        };
        $errorDetail = "forbidden: application already concluded";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

}

class TestableRegistrant extends Registrant
{
    public $id;
    public $registeredTime;
    public $concluded;
    public $note;
}
