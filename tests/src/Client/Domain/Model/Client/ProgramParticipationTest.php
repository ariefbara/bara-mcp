<?php

namespace Client\Domain\Model\Client;

use Client\Domain\{
    Event\ParticipantMutateConsultationRequestEvent,
    Event\ParticipantMutateConsultationSessionEvent,
    Model\Client,
    Model\Client\ProgramParticipation\ConsultationRequest,
    Model\Client\ProgramParticipation\ConsultationSession,
    Model\Client\ProgramParticipation\Worksheet\Comment,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\ConsultationSetup
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Shared\Domain\Model\ConsultationRequestStatusVO;
use Tests\TestBase;

class ProgramParticipationTest extends TestBase
{

    protected $program;
    protected $client;
    protected $programParticipation;
    protected $consultationRequest, $consultationRequestId = 'negotiate-consultationSession-id';
    protected $otherConsultationRequest;
    protected $consultationSession;
    protected $consultationSetup, $consultant, $startTime;
    protected $startEndTime, $newConsultationSetupConsultationRequestId = 'new-negotiate-consultationSetup-schedule-id';

    protected function setUp(): void
    {
        parent::setUp();

        $this->programParticipation = new TestableProgramParticipation();
        $this->programParticipation->active = true;
        $this->client = $this->buildMockOfClass(Client::class);
        $this->client->expects($this->any())
                ->method('getId')
                ->willReturn('clientId');
        $this->programParticipation->client = $this->client;

        $this->programParticipation->consultationRequests = new ArrayCollection();
        $this->programParticipation->consultationSessions = new ArrayCollection();


        $this->consultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->consultationRequest->expects($this->any())
                ->method('getId')
                ->willReturn($this->consultationRequestId);
        $this->otherConsultationRequest = $this->buildMockOfClass(ConsultationRequest::class);
        $this->programParticipation->consultationRequests->add($this->consultationRequest);
        $this->programParticipation->consultationRequests->add($this->otherConsultationRequest);

        $this->consultationSession = $this->buildMockOfClass(ConsultationSession::class);
        $this->programParticipation->consultationSessions->add($this->consultationSession);

        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->startTime = new DateTimeImmutable();
    }

    protected function executeQuit()
    {
        $this->programParticipation->quit();
    }

    public function test_quit_alreadyInactive_throwEx()
    {
        $this->programParticipation->active = false;
        $operation = function () {
            $this->executeQuit();
        };
        $errorDetail = 'forbidden: this request only allowed on active program participation';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_quit_setActiveFalse()
    {
        $this->executeQuit();
        $this->assertFalse($this->programParticipation->active);
    }

    public function test_quit_setNoteQuit()
    {
        $this->executeQuit();
        $this->assertEquals('quit', $this->programParticipation->note);
    }

    protected function executeCreateConsultationRequest()
    {
        $this->consultationRequest->expects($this->any())
                ->method('isConcluded')
                ->willReturn(false);
        $this->consultationRequest->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
        $this->consultationRequest->expects($this->any())
                ->method('conflictedWith')
                ->willReturn(false);
        return $this->programParticipation->createConsultationRequest(
                        $this->consultationRequestId, $this->consultationSetup, $this->consultant, $this->startTime);
    }

    public function test_createConsultationRequest_containConsultationRequestConflictedWithNewSchedule_throwEx()
    {
        $this->consultationRequest->expects($this->once())
                ->method('conflictedWith')
                ->willReturn(true);
        $operation = function () {
            $this->executeCreateConsultationRequest();
        };
        $errorDetail = "conlict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_createConsultationRequest_conflictedConsultationRequestInCollectionStatusNotEqualsProposed_ignoreThisScheduleFromAssertion()
    {
        $this->consultationRequest->expects($this->once())
                ->method('conflictedWith')
                ->willReturn(true);
        $this->consultationRequest->expects($this->once())
                ->method('statusEquals')
                ->with($this->equalTo(new ConsultationRequestStatusVO('proposed')))
                ->willReturn(false);
        $this->executeCreateConsultationRequest();
    }

    public function test_createConsultationRequest_conflictedConsultationRequestInCollectionAlreadyConcluded_ignoreThisScheduleFromAssertion()
    {
        $this->consultationRequest->expects($this->once())
                ->method('conflictedWith')
                ->willReturn(true);
        $this->consultationRequest->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $this->executeCreateConsultationRequest();
    }

    public function test_createConsultationRequest_containConsultationSessionConflictedWithNewConsultationRequest_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeCreateConsultationRequest();
        };

        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_createConsultationRequest_recordConsultationRequestMutatedByParticipantEvent()
    {
        $this->programParticipation->clearRecordedEvents();
        $this->executeCreateConsultationRequest();
        $this->assertInstanceOf(ParticipantMutateConsultationRequestEvent::class,
                $this->programParticipation->getRecordedEvents()[0]);
    }

    protected function executeReProposeConsultationRequest()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isConcluded')
                ->willReturn(false);
        $this->otherConsultationRequest->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->any())
                ->method('conflictedWith')
                ->willReturn(false);
        $this->programParticipation->reproposeConsultationRequest($this->consultationRequestId, $this->startTime);
    }

    public function test_reProposeConsultationRequest_reProposeConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('rePropose')
                ->with($this->startTime);
        $this->executeReProposeConsultationRequest();
    }

    public function test_reProposeNegotinateConsultationSession_containOtherConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('conflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeReProposeConsultationRequest();
        };
        $errorDetail = "conlict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_reProposeNegotinateConsultationSession_containConsultationRequestConflictedWithReProposedSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeReProposeConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_reProposeNegotinateConsultationSetupScXhedule_consultationRequestNotFound_throwEx()
    {
        $operation = function () {
            $this->programParticipation->reProposeConsultationRequest('non-existing-schedule', $this->startTime);
        };
        $errorDetail = "not found: consultation request not found";
        $this->assertRegularExceptionThrowed($operation, 'Not Found', $errorDetail);
    }

    public function test_rePropose_recordConsultationRequestMutatedByClientMemberEvent()
    {
        $this->programParticipation->clearRecordedEvents();
        $this->executeReProposeConsultationRequest();
        $this->assertInstanceOf(ParticipantMutateConsultationRequestEvent::class,
                $this->programParticipation->getRecordedEvents()[0]);
    }

    protected function executeAcceptConsultationRequest()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('isConcluded')
                ->willReturn(false);
        $this->otherConsultationRequest->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->any())
                ->method('conflictedWith')
                ->willReturn(false);
        $this->programParticipation->acceptConsultationRequest($this->consultationRequestId);
    }

    public function test_acceptConsultationRequest_acceptConsultationRequest()
    {
        $this->consultationRequest->expects($this->once())
                ->method('accept');
        $this->executeAcceptConsultationRequest();
    }

    public function test_acceptConsultationRequest_containOtherConsultationRequestConflictedWithThisSchedule_throwEx()
    {
        $this->otherConsultationRequest->expects($this->once())
                ->method('conflictedWith')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "conlict: requested time already occupied by your other consultation request waiting for consultant response";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_acceptConsultationRequest_containConsultationSessionConflictedWithThisSchedule_throwEx()
    {
        $this->consultationSession->expects($this->once())
                ->method('conflictedWithConsultationRequest')
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = "conflict: requested time already occupied by your other consultation session";
        $this->assertRegularExceptionThrowed($operation, 'Conflict', $errorDetail);
    }

    public function test_acceptConsultationRequest_addConsultationSessionFromConsultationRequestAndAddToCollection()
    {
        $this->consultationRequest->expects($this->once())
                ->method('createConsultationSession');
        $this->executeAcceptConsultationRequest();

        $this->assertEquals(2, $this->programParticipation->consultationSessions->count());
    }

    public function test_acceptConsultationRequest_addConsultationSessionMutatedByMemberEventToRecordedEvent()
    {
        $this->programParticipation->clearRecordedEvents();
        $this->executeAcceptConsultationRequest();
        $this->assertInstanceOf(ParticipantMutateConsultationSessionEvent::class,
                $this->programParticipation->getRecordedEvents()[0]);
    }

    public function test_createNotificationForComment_returnClientNotification()
    {
        $comment = $this->buildMockOfClass(Comment::class);
        $this->assertInstanceOf(ClientNotification::class,
                $this->programParticipation->createNotificationForComment('id', 'message', $comment));
    }

    public function test_createNotificationForConsultationRequest_returnClientNotification()
    {
        $this->assertInstanceOf(ClientNotification::class,
                $this->programParticipation->createNotificationForConsultationRequest('id', 'message',
                        $this->consultationRequest));
    }

    public function test_createNotificationForConsultationSession_returnClientNotification()
    {
        $this->assertInstanceOf(ClientNotification::class,
                $this->programParticipation->createNotificationForConsultationSession('id', 'message',
                        $this->consultationSession));
    }

    public function test_createClientNotification_returnClientNotification()
    {
        $this->assertInstanceOf(ClientNotification::class,
                $this->programParticipation->createClientNotification('id', 'message'));
    }

}

class TestableProgramParticipation extends ProgramParticipation
{

    public $program, $id = 'participantId', $client, $active, $note;
    public $consultationRequests, $consultationSessions;

    function __construct()
    {
        parent::__construct();
    }

}
