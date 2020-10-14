<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    DependencyModel\Firm\Team,
    Event\EventTriggeredByTeamMember,
    Model\Participant,
    Model\Participant\ConsultationRequest,
    Model\Participant\ConsultationSession,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment,
    Model\TeamProgramParticipation,
    Model\TeamProgramRegistration
};
use Resources\Domain\Event\CommonEvent;
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
    protected $event;
    protected $participant, $logId = "logId", $learningMaterialId = "learningMaterialId";

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
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
    }

    protected function setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($asset)
    {
        $asset->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->teamMembership->team)
                ->willReturn(false);
    }
    protected function setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($asset)
    {
        $asset->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
    }
    protected function assertAssetDoesntBelongsToTeamForbiddenError(callable $operation)
    {
        $errorDetail = "forbidden: you can only manage asset belongs to your team";
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
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramRegistration);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramRegistration);
        $operation = function () {
            $this->executeCancelTeamProgramRegistration();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
    }

    protected function executeQuitTeamProgramParticipation()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeQuitTeamProgramParticipation();
        });
    }

    protected function executeSubmitRootWorksheet()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
        return $this->teamMembership->submitRootWorksheet(
                        $this->teamProgramParticipation, $this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecordData, $this->teamMembership);
    }
    public function test_submitRootWorksheet_returnTeamProgramParticipationSubmitRootWorksheetResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitRootWorksheet")
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData,
                        $this->teamMembership);
        $this->executeSubmitRootWorksheet();
    }
    public function test_submitRootWorksheet_teamProgramParticipationDoesntBelongsToTeam_forbiddenError()
    {
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $operation = function () {
            $this->executeSubmitRootWorksheet();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
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
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
        return $this->teamMembership->submitBranchWorksheet(
                        $this->teamProgramParticipation, $this->worksheet, $this->worksheetId, $this->worksheetName, 
                        $this->mission, $this->formRecordData);
    }
    public function test_executeSubmitBranchWorksheet_returnTeamProgramParticipationSubmitBranchWorksheetResult()
    {
        $branch = $this->buildMockOfClass(Worksheet::class);
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, $this->teamMembership)
                ->willReturn($branch);
        $this->assertEquals($branch, $this->executeSubmitBranchWorksheet());
    }
    public function test_executeSubmitBranchWorksheet_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $operation = function () {
            $this->executeSubmitBranchWorksheet();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
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
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->worksheet);
        $this->teamMembership->updateWorksheet($this->worksheet, $this->worksheetName, $this->formRecordData);
    }
    public function test_updateWorksheet_executeTeamProgramParticipationsUpdateWorksheet()
    {
        $this->worksheet->expects($this->once())
                ->method("update")
                ->with($this->worksheetName, $this->formRecordData, $this->teamMembership);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->worksheet);
        $operation = function () {
            $this->executeUpdateWorksheet();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
    }

    protected function executeSubmitConsultationRequest()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
        return $this->teamMembership->submitConsultationRequest(
                        $this->teamProgramParticipation, $this->consultationRequestId, $this->consultationSetup,
                        $this->consultant, $this->startTIme);
    }
    public function test_submitConsultationRequest_returnConsultationRequest()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitConsultationRequest")
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTIme,
                        $this->teamMembership)
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeSubmitConsultationRequest();
        });
    }
    public function test_submitConsultationRequest_pullAndSaveRecordedEventsFromConsultationRequest()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitConsultationRequest")
                ->willReturn($this->consultationRequest);
        
        $this->consultationRequest->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn([$this->event]);
        
        $this->executeSubmitConsultationRequest();
        $event = new EventTriggeredByTeamMember($this->event, $this->teamMembership->id);
        $this->assertEquals($event, $this->teamMembership->recordedEvents[0]);
    }

    protected function executeChangeConsultationRequestTime()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeChangeConsultationRequestTime();
        });
    }
    public function test_changeConsultationRequestTime_pullAndSaveRecordedEventsFromTeamProgramParticipation()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn([$this->event]);
        
        $this->executeChangeConsultationRequestTime();
        $event = new EventTriggeredByTeamMember($this->event, $this->teamMembership->id);
        $this->assertEquals($event, $this->teamMembership->recordedEvents[0]);
    }

    protected function executeCancelConsultationRequest()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->consultationRequest);
        $this->teamMembership->cancelConsultationRequest($this->consultationRequest);
    }
    public function test_cancelConsulationRequest_executeTeamProgramParticipationCancelConsultationRequestMethod()
    {
        $this->consultationRequest->expects($this->once())
                ->method("cancel")
                ->with($this->teamMembership);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->consultationRequest);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeCancelConsultationRequest();
        });
    }
    public function test_cancelConsultationRequest_pullAndSaveRecordedEventsFromConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn([$this->event]);
        
        $this->executeCancelConsultationRequest();
        $event = new EventTriggeredByTeamMember($this->event, $this->teamMembership->id);
        $this->assertEquals($event, $this->teamMembership->recordedEvents[0]);
    }

    protected function executeAcceptOfferedConsultationRequest()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeAcceptOfferedConsultationRequest();
        });
    }
    public function test_acceptOfferedConsultationRequest_pullAndSaveRecordedEventsFromTeamProgramParticipation()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("pullRecordedEvents")
                ->willReturn([$this->event]);
        
        $this->executeAcceptOfferedConsultationRequest();
        $event = new EventTriggeredByTeamMember($this->event, $this->teamMembership->id);
        $this->assertEquals($event, $this->teamMembership->recordedEvents[0]);
    }

    protected function executeSubmitConsultationSessionReport()
    {
        $this->setAssetsBelongsToTeamMethodCalledAnyTimeReturnTrue($this->consultationSession);
        $this->teamMembership->submitConsultationSessionReport($this->consultationSession, $this->formRecordData);
    }
    public function test_submitConsultationSessionReport_executeTeamProgramParticipationSubmitConsultationReportMethod()
    {
        $this->consultationSession->expects($this->once())
                ->method("setParticipantFeedback")
                ->with($this->formRecordData, $this->teamMembership);
        $this->executeSubmitConsultationSessionReport();
    }
    public function test_submitConsultationSessionReport_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeSubmitConsultationSessionReport();
        });
    }
    public function test_submitConsultationSessionReport_programParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsBelongsToTeamMethodCalledOnceTimeReturnFalse($this->consultationSession);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeSubmitConsultationSessionReport();
        });
    }

    protected function executeSubmitNewCommentInWorksheet()
    {
        $this->worksheet->expects($this->any())->method("belongsToTeam")->willReturn(true);
        return $this->teamMembership->submitNewCommentInWorksheet($this->worksheet, $this->commentId,
                        $this->commentMessage);
    }
    public function test_submitNewCommentInWorksheet_returnWorksheetCreateCommentResult()
    {
        $this->worksheet->expects($this->once())
                ->method("createComment")
                ->with($this->commentId, $this->commentMessage, $this->teamMembership);
        $this->executeSubmitNewCommentInWorksheet();
    }
    public function test_submitNewCommentInWorksheet_worksheetDoesntBelongsToTeam_forbiddenError()
    {
        $this->worksheet->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeSubmitNewCommentInWorksheet();
        });
    }
    public function test_submitNewCommentInWorksheet_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
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
                ->with($this->commentId, $this->commentMessage, $this->teamMembership)
                ->willReturn($reply = $this->buildMockOfClass(Comment::class));
        $this->assertEquals($reply, $this->executeReplyComment());
    }
    public function test_replyComment_commentDoesntBelongsToTeam_forbiddenError()
    {
        $this->comment->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team)
                ->willReturn(false);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeReplyComment();
        });
    }
    public function test_replyComment_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertOperationCauseInactiveTeamMembershipForbiddenError(function () {
            $this->executeReplyComment();
        });
    }
    
    public function test_logViewLearningMaterialActivity_returnParticipantLogViewLearningMaterialActivityResult()
    {
        $this->participant->expects($this->once())
                ->method("logViewLearningMaterialActivity")
                ->with($this->logId, $this->learningMaterialId, $this->teamMembership);
        $this->teamMembership->logViewLearningMaterialActivity($this->logId, $this->participant, $this->learningMaterialId);
    }
}

class TestableTeamMembership extends TeamMembership
{
    public $recordedEvents;
    public $client;
    public $id = "memberId";
    public $team;
    public $active = true;

    function __construct()
    {
        parent::__construct();
    }

}
