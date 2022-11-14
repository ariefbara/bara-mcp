<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Client;
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

class ClientParticipantTest extends TestBase
{

    protected $clientParticipant;
    protected $client, $clientId = 'clientId', $firmId = 'firmId';
    protected $participant;
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $consultationRequestData;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $worksheet;
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;
    protected $metricAssignmentReportId = "metricAssignmentReportId", $observationTime, $metricAssignmentReportDataProvider;
    protected $fileInfo;
    protected $programsProfileForm;
    protected $participantProfile;
    
    protected $okrPeriod, $okrPeriodId = 'okrPeriodId', $okrPeriodData;
    
    protected $objective;
    protected $objectiveProgressReportId = 'objectiveProgressReportId', $objectiveProgressReportData, $objectiveProgressReport;

    protected $participantTask;
    //
    protected $task, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = new TestableClientParticipant();

        $this->client = $this->buildMockOfClass(Client::class);
//        $this->client->expects($this->any())->method('getId')->willReturn($this->clientId);
//        $this->client->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->clientParticipant->client = $this->client;

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->observationTime = new DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->participantProfile = $this->buildMockOfClass(ParticipantProfile::class);
        
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
        
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        
        $this->participantTask = $this->buildMockOfInterface(ITaskExecutableByParticipant::class);
        //
        $this->task = $this->buildMockOfInterface(ParticipantTask::class);
    }

    public function test_quit_quitParticipant()
    {
        $this->participant->expects($this->once())
                ->method('quit');
        $this->clientParticipant->quit();
    }

    protected function executeProposeConsultation()
    {
        return $this->clientParticipant->proposeConsultation(
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
        $this->clientParticipant->reproposeConsultationRequest($this->consultationRequestId, $this->consultationRequestData);
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
        $this->clientParticipant->acceptConsultationRequest($this->consultationRequestId);
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
                $this->clientParticipant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecordData));
    }
    
    public function test_submitBranchWorksheet_returnParticipantsSubmitBranchWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method("submitBranchWorksheet")
                ->with($this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData)
                ->willReturn($branch = $this->buildMockOfClass(Worksheet::class));
        $this->assertEquals($branch, $this->clientParticipant->submitBranchWorksheet(
                $this->worksheet, $this->worksheetId, $this->worksheetName, $this->mission, $this->formRecordData));
    }
    
    protected function executeReplyComment()
    {
        return $this->clientParticipant->replyComment($this->commentId, $this->comment,
                        $this->message);
    }
    public function test_replyComment_returnParticipantCommentReply()
    {
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->commentId, $this->message, null);
        $this->executeReplyComment();
    }
    
    public function test_pullRecordedEvents_returnParticipantPullRecordedEventResult()
    {
        $this->participant->expects($this->once())
                ->method("pullRecordedEvents");
        $this->clientParticipant->pullRecordedEvents();
    }
    
    protected function executeSubmitMetricAssignmentReport()
    {
        $this->clientParticipant->submitMetricAssignmentReport(
                $this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
    }
    public function test_submitMetricAssignmentReport_returnParticipantsSubmitMetricAssignmentReportResult()
    {
        $this->participant->expects($this->once())
                ->method("submitMetricAssignmentReport")
                ->with($this->metricAssignmentReportId, $this->observationTime, $this->metricAssignmentReportDataProvider);
        $this->executeSubmitMetricAssignmentReport();
    }
    public function test_submitMetricAssignmentReport_dataProviderContainAttachedFileInfoNotBelongsToClient_forbidden()
    {
        $fileInfo = $this->buildMockOfClass(FileInfo::class);
        $fileInfo->expects($this->once())
                ->method("belongsToClient")
                ->with($this->client)
                ->willReturn(false);
        $this->metricAssignmentReportDataProvider->expects($this->once())
                ->method("iterateAllAttachedFileInfo")
                ->willReturn([$fileInfo]);
        $operation = function (){
            $this->executeSubmitMetricAssignmentReport();
        };
        $errorDetail = "forbidden: unable to attach file not owned";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_ownAllAttachedFileInfo_returnTrue()
    {
        $this->assertTrue($this->clientParticipant->ownAllAttachedFileInfo($this->metricAssignmentReportDataProvider));
    }
    public function test_ownAllAttachedFileInfo_dataProviderContainFileInfoDoesntBelongsToClient_returnFalse()
    {
        $this->fileInfo->expects($this->once())
                ->method("belongsToClient")
                ->with($this->client)
                ->willReturn(false);
        $this->metricAssignmentReportDataProvider->expects($this->once())
                ->method("iterateAllAttachedFileInfo")
                ->willReturn([$this->fileInfo]);
        $this->assertFalse($this->clientParticipant->ownAllAttachedFileInfo($this->metricAssignmentReportDataProvider));
    }
    public function test_ownAllAttachedFileInfo_dataProviderContainFileInfoBelongsToClientAndNotBelongsToClient_returnFalse()
    {
        $this->fileInfo->expects($this->at(0))
                ->method("belongsToClient")
                ->with($this->client)
                ->willReturn(false);
        $this->fileInfo->expects($this->any())
                ->method("belongsToClient")
                ->with($this->client)
                ->willReturn(true);
        $this->metricAssignmentReportDataProvider->expects($this->once())
                ->method("iterateAllAttachedFileInfo")
                ->willReturn([$this->fileInfo, $this->fileInfo]);
        $this->assertFalse($this->clientParticipant->ownAllAttachedFileInfo($this->metricAssignmentReportDataProvider));
    }
    
    public function test_submitProfile_submitProfileInParticipant()
    {
        $this->participant->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->clientParticipant->submitProfile($this->programsProfileForm, $this->formRecordData);
    }
    
    public function test_removeProfile_removeProfileInParticipant()
    {
        $this->participant->expects($this->once())
                ->method("removeProfile")
                ->with($this->participantProfile);
        $this->clientParticipant->removeProfile($this->participantProfile);
    }
    
    public function test_createOKRPeriod_returnParticipantCreateOKRPeriodResult()
    {
        $this->participant->expects($this->once())
                ->method('createOKRPeriod')
                ->with($this->okrPeriodId, $this->okrPeriodData);
        $this->clientParticipant->createOKRPeriod($this->okrPeriodId, $this->okrPeriodData);
    }
    public function test_updateOKRPeriod_executeParticipantsUpdateOKRPeriod()
    {
        $this->participant->expects($this->once())
                ->method('updateOKRPeriod')
                ->with($this->okrPeriod, $this->okrPeriodData);
        $this->clientParticipant->updateOKRPeriod($this->okrPeriod, $this->okrPeriodData);
    }
    public function test_cancelOKRPeriod_participantCancelOKRPeriod()
    {
        $this->participant->expects($this->once())
                ->method('cancelOKRPeriod')
                ->with($this->okrPeriod);
        $this->clientParticipant->cancelOKRPeriod($this->okrPeriod);
    }
    
    public function test_submitObjectiveProgressReport_returnParticipantsSubmitObjectiveProgressReportResult()
    {
        $this->participant->expects($this->once())
                ->method('submitObjectiveProgressReport')
                ->with($this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
        $this->clientParticipant->submitObjectiveProgressReport($this->objective, $this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_updateObjectiveProgressReport_executeParticipantsUpdateObjectiveProgressReport()
    {
        $this->participant->expects($this->once())
                ->method('updateObjectiveProgressReport')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->clientParticipant->updateObjectiveProgressReport($this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_cancelObjectiveProgressReportSubmission_executeParticipantCancelObjectiveProgressReportSubmission()
    {
        $this->participant->expects($this->once())
                ->method('cancelObjectiveProgressReportSubmission')
                ->with($this->objectiveProgressReport);
        $this->clientParticipant->cancelObjectiveProgressReportSubmission($this->objectiveProgressReport);
    }
    
    protected function executeParticipantTask()
    {
        $this->clientParticipant->executeParticipantTask($this->participantTask);
    }
    public function test_executeParticipantTask_partcipantExecuteTask()
    {
        $this->participant->expects($this->once())
                ->method('executeParticipantTask')
                ->with($this->participantTask);
        $this->executeParticipantTask();
    }
    
    //
    protected function executeTask()
    {
        $this->clientParticipant->executeTask($this->task, $this->payload);
    }
    public function test_executeTask_executeParticipantTask()
    {
        $this->participant->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->executeTask();
    }

}

class TestableClientParticipant extends ClientParticipant
{

    public $client;
    public $id = 'participantId';
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}
