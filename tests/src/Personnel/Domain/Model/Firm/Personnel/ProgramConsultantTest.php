<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\ {
    Event\Consultant\ConsultantAcceptedConsultationRequest,
    Event\Consultant\ConsultantOfferedConsultationRequest,
    Event\Consultant\ConsultantSubmittedCommentOnWorksheet,
    Model\Firm\Personnel,
    Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    Model\Firm\Personnel\ProgramConsultant\ConsultationSession,
    Model\Firm\Program\Participant\Worksheet,
    Model\Firm\Program\Participant\Worksheet\Comment
};
use Tests\TestBase;

class ProgramConsultantTest extends TestBase
{

    protected $programConsultant;
    protected $consultationRequest, $consultationRequestId = 'negotiate-consultationSession-id';
    protected $otherConsultationRequest;
    protected $consultationSession;
    protected $startTime;
    
    protected $consultantCommentId = 'newCommentId', $worksheet, $message = 'new comment message';
    protected $comment;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = new TestableProgramConsultant();
        $this->programConsultant->consultationRequests = new ArrayCollection();
        $this->programConsultant->consultationSessions = new ArrayCollection();
        $this->programConsultant->personnel = $this->buildMockOfClass(Personnel::class);

        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->otherConsultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequest->expects($this->any())
                ->method('getId')
                ->willReturn($this->consultationRequestId);
        $this->programConsultant->consultationRequests->add($this->consultationRequest);
        $this->programConsultant->consultationRequests->add($this->otherConsultationRequest);

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->programConsultant->consultationSessions->add($this->consultationSession);

        $this->startTime = new DateTimeImmutable('+1 days');
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
    }

    protected function executeAcceptConsultationRequest()
    {
        $this->programConsultant->acceptConsultationRequest($this->consultationRequestId);
    }

    public function test_acceptConsultationRequest_acceptConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('accept');
        $this->executeAcceptConsultationRequest();
    }

    public function test_acceptConsultationRequest_consultationRequestNotFound_throwEx()
    {
        $operation = function () {
            $this->programConsultant->acceptConsultationRequest('non-existing-id');
        };
        $errorDetail = 'not found: consultation request not found';
        $this->assertRegularExceptionThrowed($operation, "Not Found", $errorDetail);
    }

    public function test_acceptConsultationRequest_addConsultationSessionFromConsultationRequestsCreateConsultationSessionToCollection()
    {
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession')
                ->willReturn($consultationSession = $this->buildMockOfClass(ConsultationSession::class));
        $this->executeAcceptConsultationRequest();
        $this->assertEquals(2, $this->programConsultant->consultationSessions->count());
        $this->assertEquals($consultationSession, $this->programConsultant->consultationSessions->last());
    }

    public function test_accept_containConsultationSessionInConflictWithNegotiateToBeAccepted_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('intersectWithConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "forbidden: you already have consultation session at designated time";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_accept_containOtherOfferedConsultationRequestInConflictWithConsultationRequestToBeAccepted_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isOfferedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = 'forbidden: you already offer designated time in other consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_accept_recordConsultantAcceptConsultationRequestEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeAcceptConsultationRequest();
        $this->assertInstanceOf(ConsultantAcceptedConsultationRequest::class,
                $this->programConsultant->getRecordedEvents()[0]);
    }

    protected function executeOfferConsultationRequestTime()
    {
        $this->consultationRequest->expects($this->atLeastOnce())
                ->method('offer');
        $this->programConsultant->offerConsultationRequestTime($this->consultationRequestId, $this->startTime);
    }

    public function test_offerConsultationRequestTime_offerTimeToConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('offer')
                ->with($this->startTime);
        $this->executeOfferConsultationRequestTime();
    }

    public function test_offer_containConsultationSessionInConflictWithOfferedConsultationRequest_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('intersectWithConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeOfferConsultationRequestTime();
        };
        $errorDetail = "forbidden: you already have consultation session at designated time";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_offer_containOtherProposedConsultationRequestInConflictWithOfferedConsultationRequest_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('isOfferedConsultationRequestConflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeOfferConsultationRequestTime();
        };
        $errorDetail = 'forbidden: you already offer designated time in other consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_offer_recordConsultantOfferedConsultationRequstEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeOfferConsultationRequestTime();
        $this->assertInstanceOf(ConsultantOfferedConsultationRequest::class, $this->programConsultant->getRecordedEvents()[0]);
    }
    
    protected function executeSubmitNewCommentOnWorksheet()
    {
        return $this->programConsultant->submitNewCommentOnWorksheet($this->consultantCommentId, $this->worksheet, $this->message);
    }
    public function test_submitNewCommentOnWorksheet_returnConsultantComment()
    {
        $comment = new Comment($this->worksheet, $this->consultantCommentId, $this->message);
        $consultantComment = new ConsultantComment($this->programConsultant, $this->consultantCommentId, $comment);
        
        $this->assertEquals($consultantComment, $this->executeSubmitNewCommentOnWorksheet());
    }
    public function test_submitNewCommentOnWorksheet_recordConsultantSubmittedCommentOnWorksheetEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeSubmitNewCommentOnWorksheet();
        $this->assertInstanceOf(ConsultantSubmittedCommentOnWorksheet::class, $this->programConsultant->getRecordedEvents()[0]);
    }
    
    protected function executeSubmitReplyOnWorksheetComment()
    {
        return $this->programConsultant->submitReplyOnWorksheetComment($this->consultantCommentId, $this->comment, $this->message);
    }
        
    public function test_submitReplyOnWorksheetComment_returnConsultantRepliedComment()
    {
        $reply = $this->buildMockOfClass(Comment::class);
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->consultantCommentId, $this->message)
                ->willReturn($reply);
        $consultantComment = new ConsultantComment($this->programConsultant, $this->consultantCommentId, $reply);
        
        $this->assertEquals($consultantComment, $this->executeSubmitReplyOnWorksheetComment());
    }

    public function test_submitReplyOnWorksheetComment_recordConsultantSubmittedCommentOnWorksheetEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeSubmitReplyOnWorksheetComment();
        $this->assertInstanceOf(ConsultantSubmittedCommentOnWorksheet::class, $this->programConsultant->getRecordedEvents()[0]);
    }
}

class TestableProgramConsultant extends ProgramConsultant
{

    public $personnel, $id = 'id', $removed;
    public $consultationRequests, $consultationSessions;

    public function __construct()
    {
        ;
    }

}
