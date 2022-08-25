<?php

namespace SharedContext\Domain\ValueObject;

use Config\EventList;
use Tests\TestBase;

class RegistrationStatusTest extends TestBase
{
    protected $registrationStatus;
    protected $otherRegistrationStatus;
    //
    protected $constructValue;
    //
    protected $programPrice = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationStatus = new TestableRegistrationStatus(RegistrationStatus::REGISTERED);
        $this->otherRegistrationStatus = new TestableRegistrationStatus(RegistrationStatus::REGISTERED);
        //
        $this->constructValue = RegistrationStatus::REGISTERED;
    }
    
    protected function construct()
    {
        return new TestableRegistrationStatus($this->constructValue);
    }
    public function test_construct_setProgramRegistrationReceivedEventEmmision()
    {
        $status = $this->construct();
        $this->assertEquals(EventList::PROGRAM_REGISTRATION_RECEIVED, $status->emittedEvent);
    }
    public function test_construct_settlementRequieredConstructionValue_setSettlementRequiredEmittedEvent()
    {
        $this->constructValue = RegistrationStatus::SETTLEMENT_REQUIRED;
        $status = $this->construct();
        $this->assertEquals(EventList::SETTLEMENT_REQUIRED, $status->emittedEvent);
    }
    
    protected function isConcluded()
    {
        return $this->registrationStatus->isConcluded();
    }
    public function test_isConcluded_registeredStatus_returnFalse()
    {
        $this->assertFalse($this->isConcluded());
    }
    public function test_isConcluded_cancelledStatus_returnTrue()
    {
        $this->registrationStatus->value = RegistrationStatus::CANCELLED;
        $this->assertTrue($this->isConcluded());
    }
    public function test_isConcluded_settlementRequiredStatus_returnTrue()
    {
        $this->registrationStatus->value = RegistrationStatus::SETTLEMENT_REQUIRED;
        $this->assertFalse($this->isConcluded());
    }
    
    protected function sameValueAs()
    {
        return $this->registrationStatus->sameValueAs($this->otherRegistrationStatus);
    }
    public function test_sameValueAs_returnTrue()
    {
        $this->assertTrue($this->sameValueAs());
    }
    public function test_sameValueAs_differentValue_returnFalse()
    {
        $this->otherRegistrationStatus = new RegistrationStatus(RegistrationStatus::ACCEPTED);
        $this->assertFalse($this->sameValueAs());
    }
    
    protected function settle()
    {
        $this->registrationStatus->value = RegistrationStatus::SETTLEMENT_REQUIRED;
        return $this->registrationStatus->settle();
    }
    public function test_settle_returnAcceptedStatus()
    {
        $accepted = new TestableRegistrationStatus(RegistrationStatus::ACCEPTED);
        $this->assertEquals($accepted, $this->settle());
    }
    public function test_settle_notSettlementRequiredStatusValue_forbidden()
    {
        $this->assertRegularExceptionThrowed(function(){
            $this->registrationStatus->settle();
        }, 'Forbidden', 'only registrant with settlement required can settle payment');
    }
    
    public function cancel()
    {
        return $this->registrationStatus->cancel();
    }
    public function test_cancel_returnCancelledState()
    {
        $status = $this->cancel();
        $this->assertEquals($status->value, RegistrationStatus::CANCELLED);
    }
    public function test_cancelled_concludedRegistration_accepted_forbidden()
    {
        $this->registrationStatus->value = RegistrationStatus::ACCEPTED;
        $this->assertRegularExceptionThrowed(function(){
            $this->cancel();
        }, 'Forbidden', 'registration already concluded');
    }
    public function test_cancelled_concludedRegistration_cancelled_forbidden()
    {
        $this->registrationStatus->value = RegistrationStatus::CANCELLED;
        $this->assertRegularExceptionThrowed(function(){
            $this->cancel();
        }, 'Forbidden', 'registration already concluded');
    }
    public function test_cancelled_concludedRegistration_rejected_forbidden()
    {
        $this->registrationStatus->value = RegistrationStatus::REJECTED;
        $this->assertRegularExceptionThrowed(function(){
            $this->cancel();
        }, 'Forbidden', 'registration already concluded');
    }
    
    protected function accept()
    {
        return $this->registrationStatus->accept($this->programPrice);
    }
    public function test_accept_returnAcceptedStatus()
    {
        $status = $this->accept();
        $this->assertSame(RegistrationStatus::ACCEPTED, $status->value);
    }
    public function test_accept_emitParticipationAcceptedEvent()
    {
        $status = $this->accept();
        $this->assertSame(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $status->getEmittedEvent());
    }
    public function test_accept_paidProgram_returnSettlementRequiredStatus()
    {
        $this->programPrice = 4000;
        $status = $this->accept();
        $this->assertSame(RegistrationStatus::SETTLEMENT_REQUIRED, $status->value);
    }
    public function test_accept_paidProgram_emitSettlementRequiredEvent()
    {
        $this->programPrice = 4000;
        $status = $this->accept();
        $this->assertSame(EventList::SETTLEMENT_REQUIRED, $status->getEmittedEvent());
    }
    public function test_accept_nonRegisteredStatus_forbidden()
    {
        $this->registrationStatus->value = RegistrationStatus::REJECTED;
        $this->assertRegularExceptionThrowed(function () {
            $this->accept();
        }, 'Forbidden', 'can only accept registered user');
    }
    
    protected function reject()
    {
        return $this->registrationStatus->reject();
    }
    public function test_reject_returnRejectedStatus()
    {
        $status = $this->reject();
        $this->assertSame(RegistrationStatus::REJECTED, $status->value);
    }
    public function test_reject_nonRegisteredStatus_forbidden()
    {
        $this->registrationStatus->value = RegistrationStatus::REJECTED;
        $this->assertRegularExceptionThrowed(function () {
            $this->reject();
        }, 'Forbidden', 'can only accept registered user');
    }
}

class TestableRegistrationStatus extends RegistrationStatus
{
    public $value;
    public $emittedEvent;
}
