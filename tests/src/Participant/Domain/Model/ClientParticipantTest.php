<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment,
    Service\MetricAssignmentReportDataProvider,
    SharedModel\FileInfo
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{

    protected $clientParticipant;
    protected $client, $clientId = 'clientId', $firmId = 'firmId';
    protected $participant;
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $startTime;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecordData;
    protected $worksheet;
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;
    protected $metricAssignmentReportId = "metricAssignmentReportId", $observationTime, $metricAssignmentReportDataProvider;
    protected $fileInfo;

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
        $this->startTime = new DateTimeImmutable();
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->observationTime = new \DateTimeImmutable();
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        $this->fileInfo = $this->buildMockOfClass(FileInfo::class);
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
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime);
    }
    public function test_proposeConsultation_returnParticipantsProposeConsultationMethod()
    {
        $consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->expects($this->once())
                ->method('submitConsultationRequest')
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime)
                ->willReturn($consultationRequest);
        $this->assertEquals($consultationRequest, $this->executeProposeConsultation());
    }

    protected function executeReProsposeConsultationRequest()
    {
        $this->clientParticipant->reproposeConsultationRequest($this->consultationRequestId, $this->startTime);
    }
    public function test_reProposeConsultationRequest_executeParticipantReproposeMethod()
    {
        $this->participant->expects($this->once())
                ->method('changeConsultationRequestTime')
                ->with($this->consultationRequestId, $this->startTime);
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

}

class TestableClientParticipant extends ClientParticipant
{

    public $client;
    public $id = 'programParticipationId';
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}
