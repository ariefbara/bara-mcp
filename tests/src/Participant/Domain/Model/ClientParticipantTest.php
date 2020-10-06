<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Client,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Program\Mission,
    Event\ClientParticipantAcceptedConsultationRequest,
    Event\ClientParticipantChangedConsultationRequestTime,
    Event\ClientParticipantProposedConsultationRequest,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
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
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;

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

        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);

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
