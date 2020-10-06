<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment,
    Model\TeamProgramParticipation,
    Model\TeamProgramRegistration,
    SharedModel\ContainActvityLog
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
    protected $consultationRequest, $consultationRequestId = "consultatioNRequestId";
    protected $consultationSetup, $consultant;
    protected $startTIme;
    protected $consultationSession;
    protected $comment, $commentId = "commentId", $commentMessage = "comment message";
    protected $activityLog;

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

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTIme = new DateTimeImmutable();

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        
        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->activityLog = $this->buildMockOfInterface(ContainActvityLog::class);
    }
    protected function assertAssetDoesntBelongsToTeamForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: you can only manage asset belongs to your team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    protected function setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("teamEquals")
                ->with($this->teamMembership->team)
                ->willReturn(false);
    }
    protected function setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue()
    {
        $this->teamProgramParticipation->expects($this->any())
                ->method("teamEquals")
                ->willReturn(true);
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
    
    public function test_setAsActivityOperator_setAsActivityLogOperator()
    {
        $this->activityLog->expects($this->once())
                ->method("setOperator")
                ->with($this->teamMembership);
        $this->teamMembership->setAsActivityOperator($this->activityLog);
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
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
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
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeQuitTeamProgramParticipation();
        });
    }

    public function test_quitTeamProgramParticipation_belongToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function () {
            $this->executeQuitTeamProgramParticipation();
        });
    }

    protected function executeSubmitRootWorksheet()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
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
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
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
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
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
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $operation = function () {
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError($operation);
    }

    public function test_submitBranchWorksheet_inactiveMembership_forbiddenError()
    {
        $this->teamMembership->active = false;
        $operation = function () {
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }

    protected function executeUpdateWorksheet()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
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
        $operation = function () {
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError($operation);
    }

    public function test_updateWorksheet_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $operation = function () {
            $this->executeUpdateWorksheet();
        };
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError($operation);
    }

    protected function executeSubmitConsultationRequest()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
        return $this->teamMembership->submitConsultationRequest(
                        $this->teamProgramParticipation, $this->consultationRequestId, $this->consultationSetup,
                        $this->consultant, $this->startTIme);
    }

    public function test_submitConsultationRequest_returnConsultationRequest()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitConsultationRequest")
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTIme, $this->teamMembership)
                ->willReturn($this->consultationRequest);
        $this->assertEquals($this->consultationRequest, $this->executeSubmitConsultationRequest());
    }

    public function test_submitConsultationRequest_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeSubmitConsultationRequest();
        });
    }

    public function test_submitConsultationRequest_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function () {
            $this->executeSubmitConsultationRequest();
        });
    }

    protected function executeChangeConsultationRequestTime()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
        $this->teamMembership->changeConsultationRequestTime(
                $this->teamProgramParticipation, $this->consultationRequestId, $this->startTIme);
    }

    public function test_changeConsultationRequestTime_executeTeamProgramParticipationChangeConsultationRequestTime()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("changeConsultationRequestTime")
                ->with($this->consultationRequestId, $this->startTIme, $this->teamMembership);
        $this->executeChangeConsultationRequestTime();
    }

    public function test_changeConsultationRequestTime_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeChangeConsultationRequestTime();
        });
    }

    public function test_changeConsultationRequestTime_belongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function () {
            $this->executeChangeConsultationRequestTime();
        });
    }

    protected function executeCancelConsultationRequest()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
        $this->teamMembership->cancelConsultationRequest($this->teamProgramParticipation, $this->consultationRequest);
    }

    public function test_cancelConsulationRequest_executeTeamProgramParticipationCancelConsultationRequestMethod()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("cancelConsultationRequest")
                ->with($this->consultationRequest, $this->teamMembership);
        $this->executeCancelConsultationRequest();
    }

    public function test_cancelConsulationRequest_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeCancelConsultationRequest();
        });
    }

    public function test_cancelConsulationRequest_belongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function () {
            $this->executeCancelConsultationRequest();
        });
    }

    protected function executeAcceptOfferedConsultationRequest()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
        $this->teamMembership->acceptOfferedConsultationRequest($this->teamProgramParticipation,
                $this->consultationRequestId);
    }

    public function test_acceptConsultationRequest_executeTeamProgramParticipationsAcceptOfferedConsultationRequestMethod()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("acceptOfferedConsultationRequest")
                ->with($this->consultationRequestId, $this->teamMembership);
        $this->executeAcceptOfferedConsultationRequest();
    }

    public function test_acceptOfferedConsultationRequest_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeAcceptOfferedConsultationRequest();
        });
    }

    public function test_acceptOfferedConsultationRequest_belongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function () {
            $this->executeAcceptOfferedConsultationRequest();
        });
    }

    protected function executeSubmitConsultationSessionReport()
    {
        $this->setAnyTeamProgramParticipationTeamEqualsMethodCallReturnTrue();
        $this->teamMembership->submitConsultationSessionReport(
                $this->teamProgramParticipation, $this->consultationSession, $this->formRecordData);
    }
    public function test_submitConsultationSessionReport_executeTeamProgramParticipationSubmitConsultationReportMethod()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitConsultationSessionReport")
                ->with($this->consultationSession, $this->formRecordData, $this->teamMembership);
        $this->executeSubmitConsultationSessionReport();
    }
    public function test_submitConsultationSessionReport_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function (){
            $this->executeSubmitConsultationSessionReport();
        });
    }
    public function test_submitConsultationSessionReport_programParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setOnceTeamProgramParticipationTeamEqualsMethodCallReturnFalse();
        $this->assertOperationCauseManageOtherTeamAsserForbiddenError(function (){
            $this->executeSubmitConsultationSessionReport();
        });
    }
    
    protected function executeSubmitNewCommentInWorksheet()
    {
        $this->worksheet->expects($this->any())->method("belongsToTeam")->willReturn(true);
        return $this->teamMembership->submitNewCommentInWorksheet($this->worksheet, $this->commentId, $this->commentMessage);
    }
    public function test_submitNewCommentInWorksheet_returnWorksheetCreateCommentResult()
    {
        $this->worksheet->expects($this->once())
                ->method("createComment")
                ->with($this->commentId, $this->commentMessage);
        $this->executeSubmitNewCommentInWorksheet();
    }
    public function test_submitNewCommentInWorksheet_worksheetDoesntBelongsToTeam_forbiddenError()
    {
        $this->worksheet->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeSubmitNewCommentInWorksheet();
        });
    }
    public function test_submitNewCommentInWorksheet_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function (){
            $this->executeSubmitNewCommentInWorksheet();
        });
    }
    
    protected function executeReplyComment()
    {
        $this->comment->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
        return $this->teamMembership->replyComment($this->comment, $this->commentId, $this->commentMessage);
    }
    public function test_replyComment_returnCommentCreateReplyResult()
    {
        $this->comment->expects($this->once())
                ->method("createReply")
                ->with($this->commentId, $this->commentMessage)
                ->willReturn($reply = $this->buildMockOfClass(Comment::class));
        $this->assertEquals($reply, $this->executeReplyComment());
    }
    public function test_replyComment_commentDoesntBelongsToTeam_forbiddenError()
    {
        $this->comment->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeReplyComment();
        });
    }
    public function test_replyComment_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function (){
            $this->executeReplyComment();
        });
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
