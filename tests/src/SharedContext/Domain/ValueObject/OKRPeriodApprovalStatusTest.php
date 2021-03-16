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
    
    public function test_isApproved_statusNoApproved_returnFalse()
    {
        $this->assertFalse($this->approvalStatus->isApproved());
    }
    public function test_isApproved_statusApproved_returnTrue()
    {
        $this->approvalStatus->value = TestableOKRPeriodApprovalStatus::APPROVED;
        $this->assertTrue($this->approvalStatus->isApproved());
    }
    
    protected function executeApprove()
    {
        return $this->approvalStatus->approve();
    }
    public function test_approve_returnApprovedApprovalStatus()
    {
        $approvalStatus = $this->executeApprove();
        $this->assertEquals($approvalStatus->value, OKRPeriodApprovalStatus::APPROVED);
    }
    public function test_approve_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->value = OKRPeriodApprovalStatus::APPROVED;
        $operation = function (){
            $this->executeApprove();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: approval status already concluded');
    }
    
    protected function executeReject()
    {
        return $this->approvalStatus->reject();
    }
    public function test_reject_returnRejectedApprovalStatus()
    {
        $approvalStatus = $this->executeReject();
        $this->assertEquals($approvalStatus->value, OKRPeriodApprovalStatus::REJECTED);
    }
    public function test_reject_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->value = OKRPeriodApprovalStatus::APPROVED;
        $operation = function (){
            $this->executeReject();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: approval status already concluded');
    }
}

class TestableOKRPeriodApprovalStatus extends OKRPeriodApprovalStatus
{
    public $value;
}
