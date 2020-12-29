<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Participant\ConsultationRequest;
use Participant\Domain\Model\Participant\ConsultationRequestData;
use Participant\Domain\Model\Participant\ParticipantProfile;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment;
use Participant\Domain\Service\MetricAssignmentReportDataProvider;
use Participant\Domain\SharedModel\FileInfo;
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
