<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class OKRPeriodApprovalStatusTest extends TestBase
{
    protected $approvalStatus;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->approvalStatus = new TestableOKRPeriodApprovalStatus(TestableOKRPeriodApprovalStatus::UNCONCLUDED);
    }
    public function test_isConcluded_returnFalse()
    {
        $this->assertFalse($this->approvalStatus->isConcluded());
    }
    public function test_isConcluded_statusNotnUnconcluded_returnFalse()
    {
        $this->approvalStatus->value = TestableOKRPeriodApprovalStatus::APPROVED;
        $this->assertTrue($this->approvalStatus->isConcluded());
    }
    
    public function test_isRejected_statusNoRejected_returnFalse()
    {
        $this->assertFalse($this->approvalStatus->isRejected());
    }
    public function test_isRejected_statusRejected_returnTrue()
    {
        $this->approvalStatus->value = TestableOKRPeriodApprovalStatus::REJECTED;
        $this->assertTrue($this->approvalStatus->isRejected());
    }
}

class TestableOKRPeriodApprovalStatus extends OKRPeriodApprovalStatus
{
    public $value;
}
