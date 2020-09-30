<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use Participant\Domain\{
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\Worksheet,
    Model\TeamProgramParticipation,
    Model\TeamProgramRegistration
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class TeamMembershipTest extends TestBase
{

    protected $teamMembership;
    protected $team;
    protected $teamProgramRegistrationId = "programRegistrationId", $program;
    protected $teamProgramRegistration;
    protected $teamProgramParticipation;
    protected $worksheetId = "worksheetId", $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $worksheet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamMembership->team = $this->team;

        $this->program = $this->buildMockOfClass(Program::class);
        $this->teamProgramRegistration = $this->buildMockOfClass(TeamProgramRegistration::class);

        $this->teamProgramParticipation = $this->buildMockOfClass(TeamProgramParticipation::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
    }
    protected function assertOperationCauseManageOtherTeamAsserForbiddenError(callable $operation): void
    {
        $errorDetail = "forbbiden: not allowed to manage asset of other team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function assertOperationCauseInactiveTeamMembershipForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeRegisterTeamToProgram()
    {
        return $this->teamMembership->registerTeamToProgram($this->teamProgramRegistrationId, $this->program);
    }

    public function test_registerTeamToProgram_returnTeamsRegisterToProgramResult()
    {
        $this->team->expects($this->once())
                ->method("registerToProgram")
                ->with($this->teamProgramRegistrationId, $this->program)
                ->willReturn($this->teamProgramRegistration);
        $this->assertEquals($this->teamProgramRegistration, $this->executeRegisterTeamToProgram());
    }

    public function test_registerTeamToProgram_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function () {
            $this->executeRegisterTeamToProgram();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }

    protected function executeCancelTeamProgramRegistration()
    {
        $this->teamProgramRegistration->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
        $this->teamMembership->cancelTeamprogramRegistration($this->teamProgramRegistration);
    }

    public function test_cancelTeamProgramRegistration_executeTeamProgramRegistrationsCancelMethod()
    {
        $this->teamProgramRegistration->expects($this->once())
                ->method("cancel");
        $this->executeCancelTeamProgramRegistration();
    }

    public function test_cancelTeamProgramRegistration_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function () {
            $this->executeCancelTeamProgramRegistration();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }

    public function test_cancelTeamProgramRegistration_teamProgramRegistrationOfDifferentTeam_forbiddenError()
    {
        $this->teamProgramRegistration->expects($this->once())
                ->method("teamEquals")
                ->with($this->team)
                ->willReturn(false);
        $operation = function () {
            $this->executeCancelTeamProgramRegistration();
        };
        $errorDetail = "forbidden: unable to alter registration from other team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeQuitTeamProgramParticipation()
    {
        $this->teamMembership->quitTeamProgramParticipation($this->teamProgramParticipation);
    }
    public function test_quitTeamProgramParticipation_quitTeamProgramParticipation()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("quit");
        $this->executeQuitTeamProgramParticipation();
    }
    public function test_quitTeamProgramParticipation_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function (){
            $this->executeQuitTeamProgramParticipation();
        });
    }

    protected function executeSubmitRootWorksheet()
    {
        $this->teamProgramParticipation->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
        return $this->teamMembership->submitRootWorksheet(
                        $this->teamProgramParticipation, $this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecordData);
    }

    public function test_submitRootWorksheet_returnTeamProgramParticipationSubmitRootWorksheetResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitRootWorksheet")
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData);
        $this->executeSubmitRootWorksheet();
    }

    public function test_submitRootWorksheet_teamProgramParticipationTeamNotEqualsTeamMembershipTeam_forbiddenError()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("teamEquals")
                ->with($this->team)
                ->willReturn(false);
        $operation = function () {
            $this->executeSubmitRootWorksheet();
        };
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError($operation);
    }

    public function test_submitRootWorksheet_inactiveMembership_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function () {
            $this->executeSubmitRootWorksheet();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }

    protected function executeSubmitBranchWorksheet()
    {
        $this->teamProgramParticipation->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
        return $this->teamMembership->submitBranchWorksheet(
                        $this->teamProgramParticipation, $this->worksheet, $this->worksheetId, $this->worksheetName,
                        $this->mission, $this->formRecordData);
    }
    public function test_executeSubmitBranchWorksheet_returnTeamProgramParticipationSubmitBranchWorksheetResult()
    {
        $branch = $this->buildMockOfClass(Worksheet::class);
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData)
                ->willReturn($branch);
        $this->assertEquals($branch, $this->executeSubmitBranchWorksheet());
    }
    public function test_executeSubmitBranchWorksheet_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("teamEquals")
                ->with($this->teamMembership->team)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError($operation);
    }
    public function test_submitBranchWorksheet_inactiveMembership_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }
    
    protected function executeUpdateWorksheet()
    {
        $this->teamProgramParticipation->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
        $this->teamMembership->updateWorksheet(
                $this->teamProgramParticipation, $this->worksheet, $this->worksheetName, $this->formRecordData);
    }
    public function test_updateWorksheet_executeTeamProgramParticipationsUpdateWorksheet()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("updateWorksheet")
                ->with($this->worksheet, $this->worksheetName, $this->formRecordData);
        $this->executeUpdateWorksheet();
    }
    public function test_updateWorksheet_inactiveTeamMembership_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }
    public function test_updateWorksheet_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("teamEquals")
                ->with($this->teamMembership->team)
                ->willReturn(false);
        $operation = function (){
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError($operation);
    }

}

class TestableTeamMembership extends TeamMembership
{

    public $client;
    public $id;
    public $team;
    public $active = true;

    function __construct()
    {
        parent::__construct();
    }

}
