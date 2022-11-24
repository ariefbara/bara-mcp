<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\SharedModel\FileInfo;
use Participant\Domain\Task\Participant\ParticipantTask;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{

    protected $userParticipant;
    protected $participant;
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $consultationRequestData;
    protected $worksheet;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;
    protected $metricAssignmentReportDataProvider, $fileInfo;
    protected $metricAssignmentReportId = "metricAssignmentReportId", $observationTime;
    protected $programsProfileForm, $profile;
    protected $okrPeriod, $okrPeriodId = 'okrPeriodId', $okrPeriodData;
    protected $objective;
    protected $objectiveProgressReportId = 'objectiveProgressReportId', $objectiveProgressReportData, $objectiveProgressReport;
    //
    protected $task, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        $this->observationTime = new DateTimeImmutable();
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->profile = $this->buildMockOfClass(ParticipantProfile::class);
        
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
        
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        //
        $this->task = $this->buildMockOfClass(ParticipantTask::class);
    }

    public function test_quit_quitParticipant()
    {
        $this->participant->expects($this->once())
                ->method('quit');
        $this->userParticipant->quit();
    }

    protected function executeProposeConsultation()
    {
        return $this->userParticipant->proposeConsultation(
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->consultationRequestData);
    }
    public function test_proposeConsultation_returnParticipantsProposeConsultationMethod()
    {
        $consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->expects($this->once())
                ->method('submitConsultationRequest')
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->consultationRequestData)
                ->willReturn($consultationRequest);
        $this->assertEquals($consultationRequest, $this->executeProposeConsultation());
    }

    protected function executeReProsposeConsultationRequest()
    {
        $this->userParticipant->reproposeConsultationRequest($this->consultationRequestId, $this->consultationRequestData);
    }
    public function test_reProposeConsultationRequest_executeParticipantReproposeMethod()
    {
        $this->participant->expects($this->once())
                ->method('changeConsultationRequestTime')
                ->with($this->consultationRequestId, $this->consultationRequestData);
        $this->executeReProsposeConsultationRequest();
    }
    
    protected function executeAcceptConsultationRequest()
    {
        $this->userParticipant->acceptConsultationRequest($this->consultationRequestId);
    }
    public function test_acceptConsultationRequest_executeParticipantsAcceptConsultationRequest()
    {
        $this->participant->expects($this->once())
                ->method('acceptOfferedConsultationRequest');
        $this->executeAcceptConsultationRequest();
    }
    
    public function test_createRootWorksheet_returnParticipantsCreateRootWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method('createRootWorksheet')
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData, null)
                ->willReturn($worksheet = $this->buildMockOfClass(Worksheet::class));

        $this->assertEquals($worksheet,
                $this->userParticipant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecordData));
    }
    
    public function test_submitBranchWorksheet_returnParticipantSubmitBranchWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData)
                ->willReturn($branch = $this->buildMockOfClass(Worksheet::class));
        $this->assertEquals($branch, $this->userParticipant->submitBranchWorksheet(
                $this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData));
    }
    
    protected function executeReplyComment()
    {
        return $this->userParticipant->replyComment($this->commentId, $this->comment,
                        $this->message);
    }
    public function test_replyComment_returnComment()
    {
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->commentId, $this->message, null);
        $this->executeReplyComment();
    }
    
    public function test_pullRecordedEvents_returnParticipantsPullRecordEventsResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->userParticipant->pullRecordedEvents();
    }
    
    protected function executeOwnAllAttachedFileInfo()
    {
        $this->fileInfo->expects($this->any())
                ->method("belongsToUser")
                ->willReturn(true);
        $this->metricAssignmentReportDataProvider->expects($this->any())
                ->method("iterateAllAttachedFileInfo")
                ->willReturn([$this->fileInfo, $this->fileInfo]);
        return $this->userParticipant->ownAllAttachedFileInfo($this->metricAssignmentReportDataProvider);
    }
    public function test_ownAllAttachedFileInfo_returnTrue()
    {
        $this->assertTrue($this->executeOwnAllAttachedFileInfo());
    }
    public function test_ownAllAttachedFileInfo_FileInfoNotBelongsToUser_returnFalse()
    {
        $this->fileInfo->expects($this->once())
                ->method("belongsToUser")
                ->with($this->userParticipant->userId)
                ->willReturn(false);
        $this->assertFalse($this->executeOwnAllAttachedFileInfo());
    }
    public function test_ownAllAttachedFileInfo_containFileInfoNotBelongsToUser_returnFalse()
    {
        $this->fileInfo->expects($this->at(1))
                ->method("belongsToUser")
                ->with($this->userParticipant->userId)
                ->willReturn(false);
        $this->assertFalse($this->executeOwnAllAttachedFileInfo());
    }
    
    public function test_submitMetricAssignmentReport_returnParticipantSubmitMetricAssignmentReportResult()
    {
        $this->participant->expects($this->once())
                ->method("submitMetricAssignmentReport")
                ->with($this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
        $this->userParticipant->submitMetricAssignmentReport(
                $this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
    }
    
    public function test_sumbmitProfile_submitProfileInParticipant()
    {
        $this->participant->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->userParticipant->submitProfile($this->programsProfileForm, $this->formRecordData);
    }
    
    public function test_removeProfile_removeProfileInParticipant()
    {
        $this->participant->expects($this->once())
                ->method("removeProfile")
                ->with($this->profile);
        $this->userParticipant->removeProfile($this->profile);
    }
    
    public function test_createOKRPeriod_returnParticipantCreateOKRPeriodResult()
    {
        $this->participant->expects($this->once())
                ->method('createOKRPeriod')
                ->with($this->okrPeriodId, $this->okrPeriodData);
        $this->userParticipant->createOKRPeriod($this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_updateOKRPeriod_executeParticipantsUpdateOKRPeriod()
    {
        $this->participant->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->okrPeriod, $this->okrPeriodData);
        $this->userParticipant->updateOKRPeriod($this->okrPeriod, $this->okrPeriodData);
    }
    public function test_cancelOKRPeriod_participantCancelOKRPeriod()
    {
        $this->participant->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->okrPeriod);
        $this->userParticipant->cancelOKRPeriod($this->okrPeriod);
    }
    
    public function test_submitObjectiveProgressReport_returnParticipantsSubmitObjectiveProgressReportResult()
    {
        $this->participant->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
        $this->userParticipant->submitObjectiveProgressReport($this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_updateObjectiveProgressReport_executeParticipantsUpdateObjectiveProgressReport()
    {
        $this->participant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->userParticipant->updateObjectiveProgressReport($this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_cancelObjectiveProgressReportSubmission_executeParticipantCancelObjectiveProgressReportSubmission()
    {
        $this->participant->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->objectiveProgressReport);
        $this->userParticipant->cancelObjectiveProgressReportSubmission($this->objectiveProgressReport);
    }
    
    //
    protected function executeTask()
    {
        $this->userParticipant->executeTask($this->task, $this->payload);
    }
    public function test_executeTask_participantExecuteTask()
    {
        $this->participant->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->executeTask();
    }

}

class TestableUserParticipant extends UserParticipant
{

    public $userId = 'userId';
    public $id = 'userParticipantId';
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}
