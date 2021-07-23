<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Resources\Application\Event\Event;
use Tests\TestBase;

class ProgramConsultantTest extends TestBase
{

    protected $programConsultant;
    protected $consultationRequest, $consultationRequestId = 'negotiate-consultationSession-id';
    protected $otherConsultationRequest;
    protected $consultationSession;
    protected $consultationRequestData;
    
    protected $consultantCommentId = 'newCommentId', $worksheet, $message = 'new comment message';
    protected $comment;
    
    protected $asset;

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

        $this->consultationRequestData = $this->buildMockOfClass(ConsultationRequestData::class);
        $this->consultationRequestData->expects($this->any())->method("getStartTime")->willReturn(new \DateTimeImmutable());
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        
        $this->asset = $this->buildMockOfInterface(IUsableInProgram::class);
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
    public function test_accept_aggregateEventFromConsultationSession()
    {
        $consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession')
                ->willReturn($consultationSession = $this->buildMockOfClass(ConsultationSession::class));
        
        $event = $this->buildMockOfInterface(Event::class);
        $consultationSession->expects($this->once())->method("pullRecordedEvents")->willReturn([$event]);
        
        $this->executeAcceptConsultationRequest();
        $this->assertEquals([$event], $this->programConsultant->recordedEvents);
    }
    public function test_accept_inactiveConsultant_forbidden()
    {
        $this->programConsultant->active = false;
        $operation = function (){
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeOfferConsultationRequestTime()
    {
        $this->consultationRequest->expects($this->any())
                ->method('offer');
        $this->programConsultant->offerConsultationRequestTime($this->consultationRequestId, $this->consultationRequestData);
    }
    public function test_offerConsultationRequestTime_offerTimeToConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('offer')
                ->with($this->consultationRequestData);
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
    public function test_offer_aggregateEventFromConsultationRequest()
    {
        $event = $this->buildMockOfInterface(Event::class);
        $this->consultationRequest->expects($this->once())->method("pullRecordedEvents")->willReturn([$event]);
        
        $this->executeOfferConsultationRequestTime();
        $this->assertEquals([$event], $this->programConsultant->recordedEvents);
    }
    public function test_offer_inactiveConsultant_forbidden()
    {
        $this->programConsultant->active = false;
        $operation = function (){
            $this->executeOfferConsultationRequestTime();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSubmitNewCommentOnWorksheet()
    {
        $this->worksheet->expects($this->any())
                ->method("belongsToParticipantInProgram")
                ->willReturn(true);
        return $this->programConsultant->submitNewCommentOnWorksheet($this->consultantCommentId, $this->worksheet, $this->message);
    }
    public function test_submitNewCommentOnWorksheet_returnConsultantComment()
    {
        $comment = new Comment($this->worksheet, $this->consultantCommentId, $this->message);
        $this->assertInstanceOf(ConsultantComment::class, $this->executeSubmitNewCommentOnWorksheet());
    }
    public function test_submitNewComment_inactiveConsultant_forbiddenError()
    {
        $this->programConsultant->active = false;
        $operation = function (){
            $this->executeSubmitNewCommentOnWorksheet();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitNewComment_worksheeNotRelatedToConsultantProgram_forbiddenError()
    {
        $this->worksheet->expects($this->once())
                ->method("belongsToParticipantInProgram")
                ->with($this->programConsultant->programId)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitNewCommentOnWorksheet();
        };
        $errorDetail = "forbidden: can only manage asset related to your program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSubmitReplyOnWorksheetComment()
    {
        $this->comment->expects($this->any())
                ->method("belongsToParticipantInProgram")
                ->willReturn(true);
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
    public function test_submitReplyOnWorksheetComment_inactiveConsultant_forbiddenError()
    {
        $this->programConsultant->active = false;
        $operation = function (){
            $this->executeSubmitReplyOnWorksheetComment();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_submitReplyOnWorksheetComment_commentNotRelatedToProgram_forbiddenError()
    {
        $this->comment->expects($this->once())
                ->method("belongsToParticipantInProgram")
                ->with($this->programConsultant->programId)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitReplyOnWorksheetComment();
        };
        $errorDetail = "forbidden: can only manage asset related to your program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_verifyAssetUsable_assertAssetUsable()
    {
        $this->asset->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->programConsultant->programId);
        $this->programConsultant->verifyAssetUsable($this->asset);
    }
}

class TestableProgramConsultant extends ProgramConsultant
{

    public $personnel, $id = 'id', $active = true;
    public $programId = "programId";
    public $consultationRequests, $consultationSessions;
    public $recordedEvents;

    public function __construct()
    {
        ;
    }

}
