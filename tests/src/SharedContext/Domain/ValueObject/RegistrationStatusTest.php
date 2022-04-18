<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class RegistrationStatusTest extends TestBase
{
    protected $registrationStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->registrationStatus = new TestableRegistrationStatus(RegistrationStatus::REGISTERED);
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
}

class TestableRegistrationStatus extends RegistrationStatus
{
    public $value;
}
