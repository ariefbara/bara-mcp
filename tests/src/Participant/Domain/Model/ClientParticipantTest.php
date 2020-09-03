<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\ClientParticipantAcceptedConsultationRequest,
    Event\ClientParticipantChangedConsultationRequestTime,
    Event\ClientParticipantProposedConsultationRequest,
    Event\Participant\Worksheet\ConsultantCommentRepliedByClientParticipant,
    Model\DependencyEntity\Firm\Client,
    Model\DependencyEntity\Firm\Program\Consultant,
    Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Model\DependencyEntity\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
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
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;

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

        $this->comment = $this->buildMockOfClass(Comment::class);
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
                ->method('proposeConsultation')
                ->with($this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime)
                ->willReturn($consultationRequest);
        $this->assertEquals($consultationRequest, $this->executeProposeConsultation());
    }
    public function test_proposeConsultation_recoredClientProposedConsultationRequestEvent()
    {
        $this->executeProposeConsultation();
        
        $event = new ClientParticipantProposedConsultationRequest(
                $this->firmId, $this->clientId, $this->clientParticipant->id, $this->consultationRequestId);
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
        
        $event = new ClientParticipantChangedConsultationRequestTime(
                $this->firmId, $this->clientId, $this->clientParticipant->id, $this->consultationRequestId);
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
        $this->assertInstanceOf(ClientParticipantAcceptedConsultationRequest::class,
                $this->clientParticipant->getRecordedEvents()[0]);
    }
    
    public function test_createRootWorksheet_returnParticipantsCreateRootWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method('createRootWorksheet')
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecord)
                ->willReturn($worksheet = $this->buildMockOfClass(Worksheet::class));

        $this->assertEquals($worksheet,
                $this->clientParticipant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecord));
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
                ->with($this->commentId, $this->message);
        $this->executeReplyComment();
    }
    public function test_replyComment_repliedCommentInConsultantComment_recordConsultantCommentRepliedByClientParticipantEvent()
    {
        $worksheetId = 'worksheetId';
        $this->comment->expects($this->once())
                ->method('isConsultantComment')
                ->willReturn(true);
        $this->comment->expects($this->once())
                ->method('getWorksheetId')
                ->willReturn($worksheetId);

        $this->executeReplyComment();

        $event = new ConsultantCommentRepliedByClientParticipant(
                $this->firmId, $this->clientId, $this->clientParticipant->id, $worksheetId, $this->commentId);
        $this->assertEquals($event, $this->clientParticipant->getRecordedEvents()[0]);
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
