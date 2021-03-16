<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Tests\src\Firm\Domain\Model\Firm\Program\CoordinatorTestBase;

class Coordinator_ManageObjectiveProgressReportTest extends CoordinatorTestBase
{
    protected $objectiveProgressReport;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
    }
    
    protected function executeApprove()
    {
        $this->setAssetManageable($this->objectiveProgressReport);
        $this->coordinator->approveObjectiveProgressReport($this->objectiveProgressReport);
    }
    public function test_approve_approveObjectiveProgressReport()
    {
        $this->objectiveProgressReport->expects($this->once())
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
    public function test_approve_unamangeObjectiveProgressReport_forbidden()
    {
        $this->setAssetUnmanageable($this->objectiveProgressReport);
        $this->assertUnmanageAsset(function (){
            $this->executeApprove();
        }, 'objective progress report');
    }
    
    protected function executeReject()
    {
        $this->setAssetManageable($this->objectiveProgressReport);
        $this->coordinator->rejectObjectiveProgressReport($this->objectiveProgressReport);
    }
    public function test_reject_rejectObjectiveProgressReport()
    {
        $this->objectiveProgressReport->expects($this->once())
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
    public function test_reject_unamangeObjectiveProgressReport_forbidden()
    {
        $this->setAssetUnmanageable($this->objectiveProgressReport);
        $this->assertUnmanageAsset(function (){
            $this->executeReject();
        }, 'objective progress report');
    }
}
