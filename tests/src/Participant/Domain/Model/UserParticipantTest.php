<?php

namespace Participant\Domain\Model;

use DateTimeImmutable;
use Participant\Domain\ {
    Event\Participant\Worksheet\ConsultantCommentRepliedByUserParticipant,
    Event\UserParticipantAcceptedConsultationRequest,
    Event\UserParticipantChangedConsultationRequestTime,
    Event\UserParticipantProposedConsultationRequest,
    Model\DependencyEntity\Firm\Program\Consultant,
    Model\DependencyEntity\Firm\Program\ConsultationSetup,
    Model\DependencyEntity\Firm\Program\Mission,
    Model\Participant\ConsultationRequest,
    Model\Participant\Worksheet,
    Model\Participant\Worksheet\Comment
};
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{

    protected $userParticipant;
    protected $participant;
    protected $consultationRequestId = 'consultationRequestId', $consultationSetup, $consultant, $startTime;
    protected $worksheetId = 'worksheetId', $worksheetName = 'worksheet name', $mission, $formRecord;
    protected $commentId = 'commentId', $message = 'message';
    protected $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;

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
        $this->userParticipant->quit();
    }

    protected function executeProposeConsultation()
    {
        return $this->userParticipant->proposeConsultation(
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
    public function test_proposeConsultation_recoredUserProposedConsultationRequestEvent()
    {
        $this->executeProposeConsultation();

        $event = new UserParticipantProposedConsultationRequest($this->userParticipant->userId, $this->userParticipant->id,
                $this->consultationRequestId);
        $this->assertEquals($event, $this->userParticipant->getRecordedEvents()[0]);
    }

    protected function executeReProsposeConsultationRequest()
    {
        $this->userParticipant->reproposeConsultationRequest($this->consultationRequestId, $this->startTime);
    }
    public function test_reProposeConsultationRequest_executeParticipantReproposeMethod()
    {
        $this->participant->expects($this->once())
                ->method('reProposeConsultationRequest')
                ->with($this->consultationRequestId, $this->startTime);
        $this->executeReProsposeConsultationRequest();
    }
    public function test_reProposeConsultationRequest_recordUserChangedConsultationRequestTimeEvent()
    {
        $this->executeReProsposeConsultationRequest();

        $event = new UserParticipantChangedConsultationRequestTime(
                $this->userParticipant->userId, $this->userParticipant->id, $this->consultationRequestId);
        $this->assertEquals($event, $this->userParticipant->getRecordedEvents()[0]);
    }
    
    protected function executeAcceptConsultationRequest()
    {
        $this->userParticipant->acceptConsultationRequest($this->consultationRequestId);
    }
    public function test_acceptConsultationRequest_executeParticipantsAcceptConsultationRequest()
    {
        $this->participant->expects($this->once())
                ->method('acceptConsultationRequest');
        $this->executeAcceptConsultationRequest();
    }
    public function test_acceptConsultationRequest_recordUserAcceptedConsultationRequestEvent()
    {
        $this->executeAcceptConsultationRequest();
        $this->assertInstanceOf(UserParticipantAcceptedConsultationRequest::class,
                $this->userParticipant->getRecordedEvents()[0]);
    }
    
    public function test_createRootWorksheet_returnParticipantsCreateRootWorksheetResult()
    {
        $this->participant->expects($this->once())
                ->method('createRootWorksheet')
                ->with($this->worksheetId, $this->worksheetName, $this->mission, $this->formRecord)
                ->willReturn($worksheet = $this->buildMockOfClass(Worksheet::class));

        $this->assertEquals($worksheet,
                $this->userParticipant->createRootWorksheet($this->worksheetId, $this->worksheetName, $this->mission,
                        $this->formRecord));
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
                ->with($this->commentId, $this->message);
        $this->executeReplyComment();
    }
    public function test_replyComment_repliedCommentIsConsultantComment_recordConsultantCommentRepliedByUserParticipantEvent()
    {
        $worksheetId = 'worksheetId';
        $this->comment->expects($this->once())
                ->method('isConsultantComment')
                ->willReturn(true);
        $this->comment->expects($this->once())
                ->method('getWorksheetId')
                ->willReturn($worksheetId);

        $this->executeReplyComment();

        $event = new ConsultantCommentRepliedByUserParticipant(
                $this->userParticipant->userId, $this->userParticipant->id, $worksheetId, $this->commentId);
        $this->assertEquals($event, $this->userParticipant->getRecordedEvents()[0]);
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
