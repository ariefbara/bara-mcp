<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class RegistrationStatusTest extends TestBase
{
    protected $registrationStatus;
    protected $otherRegistrationStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationStatus = new TestableRegistrationStatus(RegistrationStatus::REGISTERED);
        $this->otherRegistrationStatus = new TestableRegistrationStatus(RegistrationStatus::REGISTERED);
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
}

class TestableRegistrationStatus extends RegistrationStatus
{
    public $value;
}
