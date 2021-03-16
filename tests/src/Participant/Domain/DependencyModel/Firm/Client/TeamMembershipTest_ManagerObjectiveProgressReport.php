<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Tests\src\Participant\Domain\DependencyModel\Firm\Client\TeamMembershipTestBase;

class TeamMembershipTest_ManagerObjectiveProgressReport extends TeamMembershipTestBase
{
    protected $objective;
    protected $objectiveProgressReportId = 'objectiveProgressReportId';
    protected $objectiveProgressReportData;
    protected $objectiveProgressReport;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
    }
    
    protected function executeSubmitObjectiveProgressReport()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        return $this->teamMembership->submitObjectiveProgressReport(
                $this->teamParticipant, $this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_submitObjectiveProgressReport_returnObjectiveProgressReportSubmittedInTeamParticipant()
    {
        $this->teamParticipant->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
        $this->executeSubmitObjectiveProgressReport();
    }
    public function test_submitObjectiveProgressReport_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeSubmitObjectiveProgressReport();
        });
    }
    public function test_submitObjectiveProgressReport_unamnageTeamParticipant()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeSubmitObjectiveProgressReport();
        });
    }
    
    protected function executeUpdateObjectvieProgressReport()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        $this->teamMembership->updateObjectiveProgressReport(
                $this->teamParticipant, $this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_updateObjectiveProgressReport_teamParticipantUpdateObjectiveProgressReport()
    {
        $this->teamParticipant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport);
        $this->executeUpdateObjectvieProgressReport();
    }
    public function test_updateObjectiveProgressReport_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeUpdateObjectvieProgressReport();
        });
    }
    public function test_updateObjectiveProgressReport_unmanageTeamParticipant_forbidden()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeUpdateObjectvieProgressReport();
        });
    }
    
    protected function executeCancel()
    {
        $this->setAssetBelongsToTeam($this->teamParticipant);
        $this->teamMembership->cancelObjectiveProgressReportSubmission($this->teamParticipant, $this->objectiveProgressReport);
    }
    public function test_cancel_teamParticipantCancelSubmission()
    {
        $this->teamParticipant->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->objectiveProgressReport);
        $this->executeCancel();
    }
    public function test_cancel_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMemberError(function (){
            $this->executeCancel();
        });
    }
    public function test_cancel_unmanageTeamParticipant_forbidden()
    {
        $this->setAssetNotBelongsToTeam($this->teamParticipant);
        $this->assertAssetUnmanageableError(function (){
            $this->executeCancel();
        });
    }
}
