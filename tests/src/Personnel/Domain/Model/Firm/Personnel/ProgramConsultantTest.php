<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantNote;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantTask;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequestData;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ContainSchedule;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DeclaredMentoring;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlotData;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet;
use Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Personnel\Domain\Task\Mentor\MentorTask;
use Resources\Application\Event\Event;
use Resources\Domain\ValueObject\DateTimeInterval;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\LabelData;
use SharedContext\Domain\ValueObject\ScheduleData;
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
    protected $task;
    protected $consultationSessionId = 'consultationSessionId', $participant, $consultationSetup, $startEndTime, $channel;
    protected $mentoringSlotId = 'mentoringSlotId', $mentoringSlotData;
    protected $mentoringRequest;
    protected $mentoringSlot;
    protected $containSchedule;
    protected $declaredMentoringId = 'declaredMentoringId';
    protected $consultantNoteId = 'consultantNoteId', $viewableByParticipant = true;
    protected $mentorTask, $payload = 'string represent task payload';
    protected $consultantTaskId = 'consultantTaskId', $labelData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programConsultant = new TestableProgramConsultant();
        $this->programConsultant->consultationRequests = new ArrayCollection();
        $this->programConsultant->consultationSessions = new ArrayCollection();
        $this->programConsultant->personnel = $this->buildMockOfClass(Personnel::class);
        
        $this->programConsultant->mentoringRequests = new ArrayCollection();
        $this->mentoringRequest = $this->buildMockOfClass(MentoringRequest::class);
        $this->programConsultant->mentoringRequests->add($this->mentoringRequest);
        
        $this->programConsultant->mentoringSlots = new ArrayCollection();
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->programConsultant->mentoringSlots->add($this->mentoringSlot);

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
        $this->consultationRequestData->expects($this->any())->method("getStartTime")->willReturn(new DateTimeImmutable());

        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->comment = $this->buildMockOfClass(Comment::class);

        $this->asset = $this->buildMockOfInterface(IUsableInProgram::class);

        $this->task = $this->buildMockOfInterface(ITaskExecutableByMentor::class);

        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->startEndTime = $this->buildMockOfClass(DateTimeInterval::class);
        $this->channel = $this->buildMockOfClass(ConsultationChannel::class);

        $this->scheduleData = new ScheduleData(
                new \DateTimeImmutable('+24 hours'), new \DateTimeImmutable('+25 hours'), 'online',
                'http://meet.google.com/random');
        $this->mentoringSlotData = new MentoringSlotData($this->scheduleData, 4);
        
        $this->containSchedule = $this->buildMockOfInterface(ContainSchedule::class);
        //
        $this->mentorTask = $this->buildMockOfInterface(MentorTask::class);
        //
        $this->labelData = new LabelData('name', 'description');
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
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "forbidden: only active consultant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function executeOfferConsultationRequestTime()
    {
        $this->consultationRequest->expects($this->any())
                ->method('offer');
        $this->programConsultant->offerConsultationRequestTime($this->consultationRequestId,
                $this->consultationRequestData);
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
        $operation = function () {
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
        return $this->programConsultant->submitNewCommentOnWorksheet($this->consultantCommentId, $this->worksheet,
                        $this->message);
    }
    public function test_submitNewCommentOnWorksheet_returnConsultantComment()
    {
        $comment = new Comment($this->worksheet, $this->consultantCommentId, $this->message);
        $this->assertInstanceOf(ConsultantComment::class, $this->executeSubmitNewCommentOnWorksheet());
    }
    public function test_submitNewComment_inactiveConsultant_forbiddenError()
    {
        $this->programConsultant->active = false;
        $operation = function () {
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
        $operation = function () {
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
        return $this->programConsultant->submitReplyOnWorksheetComment($this->consultantCommentId, $this->comment,
                        $this->message);
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
        $operation = function () {
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
        $operation = function () {
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

    protected function executeTask()
    {
        $this->programConsultant->executeTask($this->task);
    }
    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->programConsultant);
        $this->executeTask();
    }
    public function test_executeTask_inactiveMentor_forbidden()
    {
        $this->programConsultant->active = false;
        $this->assertRegularExceptionThrowed(function () {
            $this->executeTask();
        }, 'Forbidden', 'forbidden: only active mentor can make this request');
    }

    protected function declareConsultationSession()
    {
        $this->consultationSetup->expects($this->any())
                ->method('usableInProgram')
                ->willReturn(true);
        $this->participant->expects($this->any())
                ->method('manageableInProgram')
                ->willReturn(true);
        return $this->programConsultant->declareConsultationSession(
                        $this->consultationSessionId, $this->participant, $this->consultationSetup, $this->startEndTime,
                        $this->channel);
    }
    public function test_declareConsultatioNSession_returnDeclaredConsultationSession()
    {
        $this->assertInstanceOf(ConsultationSession::class, $this->declareConsultationSession());
    }
    public function test_declareConsultationSession_unuseableConsultationSetup_forbidden()
    {
        $this->consultationSetup->expects($this->once())
                ->method('usableInProgram')
                ->with($this->programConsultant->programId)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->declareConsultationSession();
        }, 'Forbidden', 'forbidden: unuseable consultation setup');
    }
    public function test_declareConsultationSession_unmanagedParticipant_forbidden()
    {
        $this->participant->expects($this->once())
                ->method('manageableInProgram')
                ->with($this->programConsultant->programId)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->declareConsultationSession();
        }, 'Forbidden', 'forbidden: unamanged participant');
    }

    protected function createMentoringSlot()
    {
        $this->consultationSetup->expects($this->any())
                ->method('usableInProgram')
                ->with($this->programConsultant->programId)
                ->willReturn(true);
        return $this->programConsultant->createMentoringSlot(
                        $this->mentoringSlotId, $this->consultationSetup, $this->mentoringSlotData);
    }
    public function test_createMentoringSlot_returnMentoringSlot()
    {
        $this->assertInstanceOf(MentoringSlot::class, $this->createMentoringSlot());
    }
    public function test_createMentoringSlot_unuseableConsultationSetup_forbidden()
    {
        $this->consultationSetup->expects($this->once())
                ->method('usableInProgram')
                ->with($this->programConsultant->programId)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function () {
            $this->createMentoringSlot();
        }, 'Forbidden', 'forbidden: unuseable consultation setup');
    }

    protected function assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule()
    {
        $this->programConsultant->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule($this->containSchedule);
    }
    public function test_assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule_noConflicedEvent_void()
    {
        $this->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule();
        $this->markAsSuccess();
    }
    public function test_assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule_conflictWithScheduledOrOfferedRequest_forbidden()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('isScheduledOrOfferedRequestInConflictWith')
                ->with($this->containSchedule)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule();
        }, 'Forbidden', 'forbidden: schedule in conflict with scheduled or proposed request');
    }
    public function test_assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule_conflictWithActiveMentoringSlot_forbidden()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('isActiveSlotInConflictWith')
                ->with($this->containSchedule)
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule();
        }, 'Forbidden', 'forbidden: schedule in conflict with existing slot');
    }
    
    protected function declareMentoring()
    {
        $startTime = new DateTimeImmutable('-25 hours');
        $endTime = new DateTimeImmutable('-24 hours');
        $scheduleData = new ScheduleData($startTime, $endTime, 'media type', 'location');
        return $this->programConsultant->declareMentoring(
                $this->declaredMentoringId, $this->participant, $this->consultationSetup, $scheduleData);
    }
    public function test_declareMentoring_returnDeclaredMentoring()
    {
        $this->assertInstanceOf(DeclaredMentoring::class, $this->declareMentoring());
    }
    public function test_declareMentoring_inactiveMentor_forbidden()
    {
        $this->programConsultant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->declareMentoring();
        }, 'Forbidden', 'forbidden: only active consultant can make this request');
    }
    public function test_declareMentoring_assertConsultationSetupUsableInProgram()
    {
        $this->consultationSetup->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->programConsultant->programId);
        $this->declareMentoring();
    }
    public function test_declareMentoring_assertParticipantUsableInProgram()
    {
        $this->participant->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->programConsultant->programId);
        $this->declareMentoring();
    }
    
    //
    protected function submitNote()
    {
        return $this->programConsultant->submitNote($this->consultantNoteId, $this->participant, $this->labelData, $this->viewableByParticipant);
    }
    public function test_submitNote_returnConsultantNote()
    {
        $this->assertInstanceOf(ConsultantNote::class, $this->submitNote());
    }
    public function test_submitNote_assertParticipantUsableInConsultantProgram()
    {
        $this->participant->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->programConsultant->programId);
        $this->submitNote();
    }
    
    //
    protected function executeMentorTask()
    {
        $this->programConsultant->executeMentorTask($this->mentorTask, $this->payload);
    }
    public function test_executeMentorTask_executeTask()
    {
        $this->mentorTask->expects($this->once())
                ->method('execute')
                ->with($this->programConsultant, $this->payload);
        $this->executeMentorTask();
    }
    public function test_executeMentorTask_inactiveMentor_forbidden()
    {
        $this->programConsultant->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeMentorTask();
        }, 'Forbidden', 'forbidden: only active consultant can make this request');
    }
    
    //
    protected function submitTask()
    {
        return $this->programConsultant->submitTask($this->consultantCommentId, $this->participant, $this->labelData);
    }
    public function test_submitTask_returnConsultantTask()
    {
        $this->assertInstanceOf(ConsultantTask::class, $this->submitTask());
    }
    public function test_submitTask_assertParticipantUsableInProgram()
    {
        $this->participant->expects($this->once())
                ->method('assertUsableInProgram')
                ->with($this->programConsultant->programId);
        $this->submitTask();
    }
}

class TestableProgramConsultant extends ProgramConsultant
{

    public $personnel, $id = 'id', $active = true;
    public $programId = "programId";
    public $consultationRequests, $consultationSessions;
    public $recordedEvents;
    public $mentoringRequests;
    public $mentoringSlots;

    public function __construct()
    {
    }

}
