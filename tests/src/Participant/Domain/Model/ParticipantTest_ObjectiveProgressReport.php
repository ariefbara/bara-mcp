<?php

namespace Participant\Domain\Model;

use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Tests\src\Participant\Domain\Model\ParticipantTestBase;

class ParticipantTest_ObjectiveProgressReport extends ParticipantTestBase
{
    protected $objective;
    protected $objectiveProgressReportId = 'objectiveProgressReportId';
    protected $objectiveProgressReportData;
    protected $objectiveProgressReport;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
    }
    
    protected function executeSubmit()
    {
        $this->setAssetManageable($this->objective);
        return $this->participant->submitObjectiveProgressReport(
                $this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_submit_returnObjectivesSubmitReportResult()
    {
        $this->objective->expects($this->once())
                ->method('submitReport')
                ->with($this->objectiveProgressReportId, $this->objectiveProgressReportData);
        $this->executeSubmit();
    }
    public function test_submit_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeSubmit();
        });
    }
    public function test_submit_unmanageObjective_forbidden()
    {
        $this->setAssetUnmanageable($this->objective);
        $this->assertUnmanageableByParticipantError(function(){
            $this->executeSubmit();
        }, 'objective');
    }
    
    protected function executeUpdate()
    {
        $this->setAssetManageable($this->objectiveProgressReport);
        $this->participant->updateObjectiveProgressReport($this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_update_updateObjectiveProgressReport()
    {
        $this->objectiveProgressReport->expects($this->once())
                ->method('update')
                ->with($this->objectiveProgressReportData);
        $this->executeUpdate();
    }
    public function test_update_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeUpdate();
        });
    }
    public function test_update_unmanageObjectiveProgressReport_forbidden()
    {
        $this->setAssetUnmanageable($this->objectiveProgressReport);
        $this->assertUnmanageableByParticipantError(function(){
            $this->executeUpdate();
        }, 'objective progress report');
    }
    
    protected function executeCancel()
    {
        $this->setAssetManageable($this->objectiveProgressReport);
        $this->participant->cancelObjectiveProgressReportSubmission($this->objectiveProgressReport);
    }
    public function test_cancel_cancelObjectiveProgressReport()
    {
        $this->objectiveProgressReport->expects($this->once())
                ->method('cancel');
        $this->executeCancel();
    }
    public function test_cancel_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $this->assertInactiveParticipantError(function (){
            $this->executeCancel();
        });
    }
    public function test_cancel_unmanageObjectiveProgressReport_forbidden()
    {
        $this->setAssetUnmanageable($this->objectiveProgressReport);
        $this->assertUnmanageableByParticipantError(function(){
            $this->executeCancel();
        }, 'objective progress report');
    }
}
