<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\ClientAcceptedConsultationRequest,
    Event\ClientChangedConsultationRequestTime,
    Event\ClientProposedConsultationRequest,
    Event\Participant\Worksheet\ConsultantCommentRepliedByClientParticipant,
    Model\DependencyEntity\Firm\Client,
    Model\DependencyEntity\Firm\Program\Consultant,
    Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Model\DependencyEntity\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\ConsultantComment,
    Model\Participant\Worksheet\ParticipantComment
};
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use Tests\TestBase;

class ClientParticipantTest extends TestBase
{

    protected $clientParticipant;
    protected $client, $clientId = 'clientId', $firmId = 'firmId';
    protected $participant;
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $startTime;
    
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecord;
    
    protected $participantCommentId = 'participantCommentId', $message = 'message';
    protected $consultantComment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientParticipant = new TestableClientParticipant();
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->client->expects($this->any())->method('getId')->willReturn($this->clientId);
        $this->client->expects($this->any())->method('getFirmId')->willReturn($this->firmId);
        $this->clientParticipant->client = $this->client;
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->clientParticipant->participant = $this->participant;

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTime = new DateTimeImmutable();
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        
        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
    }

    public function test_quit_quitParticipant()
    {
        $this->participant->expects($this->once())
                ->method('quit');
        $this->clientParticipant->quit();
    }

    protected function executeProposeConsultation()
    {
        $this->consultationSetup->expects($this->any())
                ->method('programEquals')
                ->willReturn(true);
        
        $this->consultant->expects($this->any())
                ->method('programEquals')
                ->willReturn(true);
        
        return $this->clientParticipant->proposeConsultation(
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime);
    }
    public function test_proposeConsultation_returnParticipantsProposeConsultationMethod()
    {
        $consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->participant->expects($this->once())
                ->method('proposeConsultation')
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime)
                ->willReturn($consultationRequest);
        $this->assertEquals($consultationRequest, $this->executeProposeConsultation());
    }
    public function test_proposeConsultation_consultationSetupProgramHasDifferentProgramId_forbiddenError()
    {
        $this->consultationSetup->expects($this->once())
                ->method('programEquals')
                ->with($this->clientParticipant->program)
                ->willReturn(false);
        
        $operation = function (){
            $this->executeProposeConsultation();
        };
        $errorDetail = 'forbidden: consultation setup from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_proposeConsultation_consultantFromDifferentProgram_forbiddenError()
    {
        $this->consultant->expects($this->once())
                ->method('programEquals')
                ->with($this->clientParticipant->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeProposeConsultation();
        };
        $errorDetail = 'forbidden: consultant from different program';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    public function test_proposeConsultation_recoredClientProposedConsultationRequestEvent()
    {
        $firmId = 'firmId';
        $clientId = 'clientId';
        $programId = 'programId';
        
        $this->program->expects($this->any())->method('getId')->willReturn($programId);
        $this->client->expects($this->any())->method('getId')->willReturn($clientId);
        $this->client->expects($this->any())->method('getFirmId')->willReturn($firmId);
        
        $this->executeProposeConsultation();
        
        $event = new ClientProposedConsultationRequest($firmId, $clientId, $programId, $this->consultationRequestId);
        $this->assertEquals($event, $this->clientParticipant->getRecordedEvents()[0]);
    }
    
    protected function executeReProsposeConsultationRequest()
    {
        $this->clientParticipant->reproposeConsultationRequest($this->consultationRequestId, $this->startTime);
    }
    public function test_reProposeConsultationRequest_executeParticipantReproposeMethod()
    {
        $this->participant->expects($this->once())
                ->method('reProposeConsultationRequest')
                ->with($this->consultationRequestId, $this->startTime);
        $this->executeReProsposeConsultationRequest();
    }
    public function test_reProposeConsultationRequest_recordClientChangedConsultationRequestTimeEvent()
    {
        $this->executeReProsposeConsultationRequest();
        $event = new ClientChangedConsultationRequestTime($this->firmId, $this->clientId, $this->programId, $this->consultationRequestId);
        $this->assertEquals($event, $this->clientParticipant->getRecordedEvents()[0]);
    }
    
    protected function executeAcceptConsultationRequest()
    {
        $this->clientParticipant->acceptConsultationRequest($this->consultationRequestId);
    }
    public function test_acceptConsultationRequest_executeParticipantsAcceptConsultationRequest()
    {
        $this->participant->expects($this->once())
                ->method('acceptConsultationRequest');
        $this->executeAcceptConsultationRequest();
    }
    public function test_acceptConsultationRequest_recordClientAcceptedConsultationRequestEvent()
    {
        $this->executeAcceptConsultationRequest();
        $this->assertInstanceOf(ClientAcceptedConsultationRequest::class, $this->clientParticipant->getRecordedEvents()[0]);
    }
    
    public function test_createRootWorksheet_returnParticipantsCreateRootWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method('createRootWorksheet')
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecord)
                ->willReturn($worksheet = $this->buildMockOfClass(Worksheet::class));
        
        $this->assertEquals($worksheet, $this->clientParticipant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecord));
    }
    
    protected function executeReplyToConsultantComment()
    {
        return $this->clientParticipant->replyToConsultantComment($this->participantCommentId, $this->consultantComment, $this->message);
    }
    public function test_replyToCosultantComment_returnParticipantCommentReply()
    {
        $participantComment = $this->buildMockOfClass(ParticipantComment::class);
        $this->consultantComment->expects($this->once())
                ->method('createReply')
                ->with($this->participantCommentId, $this->message)
                ->willReturn($participantComment);
        
        $this->assertEquals($participantComment, $this->executeReplyToConsultantComment());
    }
    public function test_replyToConsultantComment_recordConsultantCommentRepliedByClientParticipantEvent()
    {
        $worksheetId = 'worksheetId';
        $this->consultantComment->expects($this->once())
                ->method('getWorksheetId')
                ->willReturn($worksheetId);
        
        $this->executeReplyToConsultantComment();
        
        $event = new ConsultantCommentRepliedByClientParticipant($this->firmId, $this->clientId, $this->programId, $worksheetId, $this->participantCommentId);
        $this->assertEquals($event, $this->clientParticipant->getRecordedEvents()[0]);
    }

}

class TestableClientParticipant extends ClientParticipant
{

    public $client;
    public $id;
    public $participant;

    function __construct()
    {
        parent::__construct();
    }

}
