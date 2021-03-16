<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Tests\src\Firm\Domain\Model\Firm\Program\CoordinatorTestBase;

class Coordinator_ManageOKRPeriodTest extends CoordinatorTestBase
{
    protected $okrPeriod;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
    }
    
    protected function executeApprove()
    {
        $this->setAssetManageable($this->okrPeriod);
        return $this->coordinator->approveOKRPeriod($this->okrPeriod);
    }
    public function test_approve_approveOKRPeriod()
    {
        $this->okrPeriod->expects($this->once())
                ->method('approve');
        $this->executeApprove();
    }
    public function test_approve_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinator(function (){
            $this->executeApprove();
        });
    }
    public function test_approve_unamangeOKRPeriod_forbidden()
    {
        $this->setAssetUnmanageable($this->okrPeriod);
        $this->assertUnmanageAsset(function (){
            $this->executeApprove();
        }, 'okr period');
    }
    
    protected function executeReject()
    {
        $this->setAssetManageable($this->okrPeriod);
        $this->coordinator->rejectOKRPeriod($this->okrPeriod);
    }
    public function test_reject_rejectOKRPeriod()
    {
        $this->okrPeriod->expects($this->once())
                ->method('reject');
        $this->executeReject();
    }
    public function test_reject_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinator(function (){
            $this->executeReject();
        });
    }
    public function test_reject_unamangeOKRPeriod_forbidden()
    {
        $this->setAssetUnmanageable($this->okrPeriod);
        $this->assertUnmanageAsset(function (){
            $this->executeReject();
        }, 'okr period');
    }
}
