<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\{
    Event\ConsultantMutateConsultationRequestEvent,
    Event\ConsultantMutateConsultationSessionEvent,
    Model\Firm\Personnel,
    Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Shared\Domain\Model\ConsultationRequestStatusVO;
use Tests\TestBase;

class ProgramConsultantTest extends TestBase
{

    protected $programConsultant;
    protected $consultationRequest, $consultationRequestId = 'negotiate-consultationSession-id';
    protected $otherConsultationRequest;
    protected $consultationSession;
    protected $startTime;

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

    public function test_accept_containOtherProposedConsultationRequestInConflictWithConsultationRequestToBeAccepted_throwEx()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('statusEquals')
                ->with($this->equalTo(new ConsultationRequestStatusVO("offered")))
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->once())
                ->method('intersectWithOtherConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeAcceptConsultationRequest();
        };
        $errorDetail = 'forbidden: you already offer designated time in other consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_accept_otherConflicedConsultationRequestStatusVONotProposed_processNormally()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('intersectWithOtherConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->once())
                ->method('statusEquals')
                ->with($this->equalTo(new ConsultationRequestStatusVO("offered")))
                ->willReturn(false);
        $this->executeAcceptConsultationRequest();
    }

    public function test_accept_otherProposedAndConflictedConsultationRequestAlreadyConcluded_processNormallly()
    {
        $this->otherConsultationRequest->expects($this->any())
                ->method('intersectWithOtherConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->any())
                ->method('statusEquals')
                ->with($this->equalTo(new ConsultationRequestStatusVO("offered")))
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $this->executeAcceptConsultationRequest();
    }

    public function test_accept_recordConsultationSessionMutatedByConsultantEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeAcceptConsultationRequest();
        $this->assertInstanceOf(ConsultantMutateConsultationSessionEvent::class,
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
                ->method('statusEquals')
                ->with($this->equalTo(new ConsultationRequestStatusVO("offered")))
                ->willReturn(true);
        $this->otherConsultationRequest->expects($this->once())
                ->method('intersectWithOtherConsultationRequest')
                ->with($this->consultationRequest)
                ->willReturn(true);
        $operation = function () {
            $this->executeOfferConsultationRequestTime();
        };
        $errorDetail = 'forbidden: you already offer designated time in other consultation request';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    public function test_offer_recordConsultationRequestMutatedByConsultantEvent()
    {
        $this->programConsultant->clearRecordedEvents();
        $this->executeOfferConsultationRequestTime();
        $this->assertInstanceOf(ConsultantMutateConsultationRequestEvent::class,
                $this->programConsultant->getRecordedEvents()[0]);
    }

    public function test_createNotificationForConsultationRequest_returnPersonnelNotification()
    {
        $this->assertInstanceOf(PersonnelNotification::class,
                $this->programConsultant->createNotificationForConsultationRequest('id', 'message',
                        $this->consultationRequest));
    }
    public function test_createNotificationForConsultationSession_returnPersonnelNotification()
    {
        $this->assertInstanceOf(PersonnelNotification::class,
                $this->programConsultant->createNotificationForConsultationSession('id', 'message',
                        $this->consultationSession));
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
