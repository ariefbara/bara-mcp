<?php

namespace Participant\Domain\DependencyModel\Firm\Client;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client\TeamMember\MemberComment;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Event\EventTriggeredByTeamMember;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\ConsultationSession;
use Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Participant\Domain\Model\TeamProgramParticipation;
use Participant\Domain\Model\TeamProgramRegistration;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\Task\Participant\ParticipantTask;
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
    protected $consultationRequestData;
    protected $consultationSession, $mentorRating = 4;
    protected $comment, $commentId = "commentId", $commentMessage = "comment message";
    protected $event;
    protected $participant, $logId = "logId", $learningMaterialId = "learningMaterialId";
    protected $metricAssignmentReportId = "metricAssignmentReportId", $metricAssignmentReport, $metricAssignmentReportDataProvider;
    protected $observationTime;
    protected $programRegistration;
    protected $programsProfileForm, $registrantProfile;
    protected $participantProfile;
    protected $participantTask;
    //
    protected $task, $payload = 'string represent task payload';

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
        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);

        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->event = $this->buildMockOfClass(CommonEvent::class);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        
        $this->programRegistration = $this->buildMockOfClass(TeamProgramRegistration::class);
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->registrantProfile = $this->buildMockOfClass(RegistrantProfile::class);
        
        $this->participantProfile = $this->buildMockOfClass(ParticipantProfile::class);
        
        $this->participantTask = $this->buildMockOfInterface(\Participant\Domain\Model\ITaskExecutableByParticipant::class);
        //
        $this->task = $this->buildMockOfInterface(ParticipantTask::class);
    }

    protected function setAssetsNotBelongsToTeam($asset)
    {
        $asset->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->teamMembership->team)
                ->willReturn(false);
    }
    protected function setAssetsBelongsToTeam($asset)
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
    protected function assertInactiveTeamMembershipForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

//    protected function executeRegisterTeamToProgram()
//    {
//        return $this->teamMembership->registerTeamToProgram($this->teamProgramRegistrationId, $this->program);
//    }
//    public function test_registerTeamToProgram_returnTeamsRegisterToProgramResult()
//    {
//        $this->team->expects($this->once())
//                ->method("registerToProgram")
//                ->with($this->teamProgramRegistrationId, $this->program)
//                ->willReturn($this->teamProgramRegistration);
//        $this->assertEquals($this->teamProgramRegistration, $this->executeRegisterTeamToProgram());
//    }
//    public function test_registerTeamToProgram_inactiveMember_forbiddenError()
//    {
//        $this->teamMembership->active = false;
//        $operation = function () {
//            $this->executeRegisterTeamToProgram();
//        };
//        $this->assertInactiveTeamMembershipForbiddenError($operation);
//    }

    protected function executeCancelTeamProgramRegistration()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramRegistration);
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
        $this->assertInactiveTeamMembershipForbiddenError($operation);
    }
    public function test_cancelTeamProgramRegistration_teamProgramRegistrationOfDifferentTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramRegistration);
        $operation = function () {
            $this->executeCancelTeamProgramRegistration();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
    }

    protected function executeQuitTeamProgramParticipation()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
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
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeQuitTeamProgramParticipation();
        });
    }
    public function test_quitTeamProgramParticipation_belongToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function () {
            $this->executeQuitTeamProgramParticipation();
        });
    }

    protected function executeSubmitRootWorksheet()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
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
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
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
        $this->assertInactiveTeamMembershipForbiddenError($operation);
    }

    protected function executeSubmitBranchWorksheet()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
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
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
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
        $this->assertInactiveTeamMembershipForbiddenError($operation);
    }

    protected function executeUpdateWorksheet()
    {
        $this->setAssetsBelongsToTeam($this->worksheet);
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
        $this->assertInactiveTeamMembershipForbiddenError($operation);
    }
    public function test_updateWorksheet_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->worksheet);
        $operation = function () {
            $this->executeUpdateWorksheet();
        };
        $this->assertAssetDoesntBelongsToTeamForbiddenError($operation);
    }

    protected function executeSubmitConsultationRequest()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
        return $this->teamMembership->submitConsultationRequest(
                        $this->teamProgramParticipation, $this->consultationRequestId, $this->consultationSetup,
                        $this->consultant, $this->consultationRequestData);
    }
    public function test_submitConsultationRequest_returnConsultationRequest()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitConsultationRequest")
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->consultationRequestData,
                        $this->teamMembership)
                ->willReturn($this->consultationRequest);
        $this->assertEquals($this->consultationRequest, $this->executeSubmitConsultationRequest());
    }
    public function test_submitConsultationRequest_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeSubmitConsultationRequest();
        });
    }
    public function test_submitConsultationRequest_teamProgramParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
        $this->teamMembership->changeConsultationRequestTime(
                $this->teamProgramParticipation, $this->consultationRequestId, $this->consultationRequestData);
    }
    public function test_changeConsultationRequestTime_executeTeamProgramParticipationChangeConsultationRequestTime()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("changeConsultationRequestTime")
                ->with($this->consultationRequestId, $this->consultationRequestData, $this->teamMembership);
        $this->executeChangeConsultationRequestTime();
    }
    public function test_changeConsultationRequestTime_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeChangeConsultationRequestTime();
        });
    }
    public function test_changeConsultationRequestTime_belongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeam($this->consultationRequest);
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
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeCancelConsultationRequest();
        });
    }
    public function test_cancelConsulationRequest_belongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->consultationRequest);
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
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
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
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeAcceptOfferedConsultationRequest();
        });
    }
    public function test_acceptOfferedConsultationRequest_belongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
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
        $this->setAssetsBelongsToTeam($this->consultationSession);
        $this->teamMembership->submitConsultationSessionReport($this->consultationSession, $this->formRecordData, $this->mentorRating);
    }
    public function test_submitConsultationSessionReport_executeTeamProgramParticipationSubmitConsultationReportMethod()
    {
        $this->consultationSession->expects($this->once())
                ->method("setParticipantFeedback")
                ->with($this->formRecordData, $this->mentorRating, $this->teamMembership);
        $this->executeSubmitConsultationSessionReport();
    }
    public function test_submitConsultationSessionReport_inactiveMember_forbiddenError()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function () {
            $this->executeSubmitConsultationSessionReport();
        });
    }
    public function test_submitConsultationSessionReport_programParticipationBelongsToOtherTeam_forbiddenError()
    {
        $this->setAssetsNotBelongsToTeam($this->consultationSession);
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
    public function test_submitNewCommentInWorksheet_returnMemberComment()
    {
        $this->worksheet->expects($this->once())
                ->method("createComment")
                ->with($this->commentId, $this->commentMessage, $this->teamMembership)
                ->willReturn($this->comment);
        $memberComment = new TeamMember\MemberComment($this->teamMembership, $this->comment);
        $this->assertEquals($memberComment, $this->executeSubmitNewCommentInWorksheet());
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
        $this->assertInactiveTeamMembershipForbiddenError(function () {
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
    public function test_replyComment_returnMemberComment()
    {
        $this->comment->expects($this->once())
                ->method("createReply")
                ->with($this->commentId, $this->commentMessage, $this->teamMembership)
                ->willReturn($reply = $this->buildMockOfClass(Comment::class));
        $memberComment = new MemberComment($this->teamMembership, $reply);
        $this->assertEquals($memberComment, $this->executeReplyComment());
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
        $this->assertInactiveTeamMembershipForbiddenError(function () {
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
    
    protected function executeSubmitReportInMetricAssignment()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
        return $this->teamMembership->submitMetricAssignmentReport(
                $this->teamProgramParticipation, $this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
    }
    public function test_submitReportInMetricAssignment_returnMetricAssignmentSubmitReportResult()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitMetricAssignmentReport")
                ->with($this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
        $this->executeSubmitReportInMetricAssignment();
    }
    public function test_submitReportInMetricAssignment_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeSubmitReportInMetricAssignment();
        });
    }
    public function test_submitReportInMetricAssignment_metricAssignmentDoesntBelongsToTeam_forbidden()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeSubmitReportInMetricAssignment();
        });
    }
    
    protected function executeUpdateMetricAssignmentReport()
    {
        $this->setAssetsBelongsToTeam($this->metricAssignmentReport);
        $this->teamMembership->updateMetricAssignmentReport($this->metricAssignmentReport, $this->metricAssignmentReportDataProvider);
    }
    public function test_updateMetricAssignmentReport_updateMetricAssignmentReport()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("update")
                ->with($this->metricAssignmentReportDataProvider);
        $this->executeUpdateMetricAssignmentReport();
    }
    public function test_updateMetricAssignmentReport_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeUpdateMetricAssignmentReport();
        });
    }
    public function test_updateMetricAssignmentReport_metricAssignmentReportDoesntBelongsToTeam_forbidden()
    {
        $this->setAssetsNotBelongsToTeam($this->metricAssignmentReport);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeUpdateMetricAssignmentReport();
        });
    }
    
    protected function executeSubmitRegistrantProfile()
    {
        $this->setAssetsBelongsToTeam($this->programRegistration);
        $this->teamMembership->submitRegistrantProfile(
                $this->programRegistration, $this->programsProfileForm, $this->formRecordData);
    }
    public function test_submitRegistrantProfile_submitProfileInProgramRegistration()
    {
        $this->programRegistration->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->executeSubmitRegistrantProfile();
    }
    public function test_submitRegistrantProfile_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeSubmitRegistrantProfile();
        });
    }
    public function test_submitRegistrantProfile_programRegistrationNotFromSameTeam()
    {
        $this->setAssetsNotBelongsToTeam($this->programRegistration);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeSubmitRegistrantProfile();
        });
    }
    
    protected function executeRemoveRegistrantProfile()
    {
        $this->setAssetsBelongsToTeam($this->programRegistration);
        $this->teamMembership->removeRegistrantProfile($this->programRegistration, $this->registrantProfile);
    }
    public function test_removeRegistrantProfile_removeProgramRegistrationProfile()
    {
        $this->programRegistration->expects($this->once())
                ->method("removeProfile")
                ->with($this->registrantProfile);
        $this->executeRemoveRegistrantProfile();
    }
    public function test_removeRegistrantProfile_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeRemoveRegistrantProfile();
        });
    }
    public function test_removeRegistrantProfile_teamDoesntOwnProgramRegistration_forbidden()
    {
        $this->setAssetsNotBelongsToTeam($this->programRegistration);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeRemoveRegistrantProfile();
        });
    }
    
    protected function executeSubmitParticipantProfile()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
        $this->teamMembership->submitParticipantProfile(
                $this->teamProgramParticipation, $this->programsProfileForm, $this->formRecordData);
    }
    public function test_submitParticipantProfile_submitProfileInProgramParticipation()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->executeSubmitParticipantProfile();
    }
    public function test_submitParticipantProfile_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeSubmitParticipantProfile();
        });
    }
    public function test_submitParticipantProfile_programParticipationNotFromSameTeam()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeSubmitParticipantProfile();
        });
    }
    
    protected function executeRemoveParticipantProfile()
    {
        $this->setAssetsBelongsToTeam($this->teamProgramParticipation);
        $this->teamMembership->removeParticipantProfile($this->teamProgramParticipation, $this->participantProfile);
    }
    public function test_removeParticipantProfile_removeProfileInTeamParticipant()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method("removeProfile")
                ->with($this->participantProfile);
        $this->executeRemoveParticipantProfile();
    }
    public function test_removeParticipantProfile_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertInactiveTeamMembershipForbiddenError(function (){
            $this->executeRemoveParticipantProfile();
        });
    }
    public function test_removeParticipantProfile_teamParticipantNotFromSameTeam_forbidden()
    {
        $this->setAssetsNotBelongsToTeam($this->teamProgramParticipation);
        $this->assertAssetDoesntBelongsToTeamForbiddenError(function (){
            $this->executeRemoveParticipantProfile();
        });
    }
    
    protected function executeTeamParticipantTask()
    {
        $this->teamMembership->executeTeamParticipantTask($this->teamProgramParticipation, $this->participantTask);
    }
    public function test_executeTeamParticipantTask_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeTeamParticipantTask();
        }, 'Forbidden', 'forbidden: only active team member can make this request');
    }
    public function test_executeTeamParticipantTask_teamParticipantExecuteParticipantTask()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('executeParticipantTask')
                ->with($this->participantTask);
        $this->executeTeamParticipantTask();
    }
    public function test_executeTeamParticipantTask_assertTeamParticipantBelongsToTeam()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('assertBelongsToTeam')
                ->with($this->team);
        $this->executeTeamParticipantTask();
    }
    
    //
    protected function executeParticipantTask()
    {
        $this->teamMembership->executeParticipantTask($this->teamProgramParticipation, $this->task, $this->payload);
    }
    public function test_executeParticipantTask_teamParticipantExecuteTask()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->executeParticipantTask();
    }
    public function test_executeParticipantTask_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeParticipantTask();
        }, 'Forbidden', 'only active team member can make this request');
    }
    public function test_executeParticipantTask_asserTeamParticipantBelongsToTeam()
    {
        $this->teamProgramParticipation->expects($this->once())
                ->method('assertBelongsToTeam')
                ->with($this->team);
        $this->executeParticipantTask();
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
