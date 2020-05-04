<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Personnel\Domain\{
    Event\ConsultantMutateConsultationRequestEvent,
    Event\ConsultantMutateConsultationSessionEvent,
    Model\Firm\Personnel,
    Model\Firm\Personnel\ProgramConsultant\ConsultationRequest,
    Model\Firm\Personnel\ProgramConsultant\ConsultationSession
};
use Query\Domain\Model\Firm\Program;
use Resources\{
    Domain\Model\ModelContainEvents,
    Exception\RegularException,
    Uuid
};
use Shared\Domain\Model\ConsultationRequestStatusVO;

class ProgramConsultant extends ModelContainEvents
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequests;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessions;

    function getPersonnel(): Personnel
    {
        return $this->personnel;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function acceptConsultationRequest(string $consultationRequestId): void
    {
        $consultationRequest = $this->findConsultationRequestOrDie($consultationRequestId);

        $this->assertNoConsultationSessionInConflictWithConsultationRequest($consultationRequest);
        $this->assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
                $consultationRequest);

        $consultationRequest->accept();
        $ConsultationSessionId = Uuid::generateUuid4();
        $consultationSession = $consultationRequest->createConsultationSession($ConsultationSessionId);
        $this->consultationSessions->add($consultationSession);

        $messageForClient = "consultation with consultant {$this->personnel->getName()} has been scheduled";
        $event = new ConsultantMutateConsultationSessionEvent(
                $this->personnel->getFirm()->getId(), $this->personnel->getId(), $this->id, $ConsultationSessionId,
                $messageForClient);
        $this->recordEvent($event);
    }

    public function offerConsultationRequestTime(string $consultationRequestId, DateTimeImmutable $startTime): void
    {
        $consultationRequest = $this->findConsultationRequestOrDie($consultationRequestId);
        $consultationRequest->offer($startTime);

        $this->assertNoConsultationSessionInConflictWithConsultationRequest($consultationRequest);
        $this->assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
                $consultationRequest);

        $messageForClient = "consultant {$this->personnel->getName()} has offer new consultation time to you";
        $event = new ConsultantMutateConsultationRequestEvent(
                $this->personnel->getFirm()->getId(), $this->personnel->getId(), $this->id,
                $consultationRequest->getId(), $messageForClient);
        $this->recordEvent($event);
    }

    protected function assertNoConsultationSessionInConflictWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $p = function (ConsultationSession $consultationSession) use ($consultationRequest) {
            return $consultationSession->intersectWithConsultationRequest($consultationRequest);
        };
        if (!empty($this->consultationSessions->filter($p)->count())) {
            $errorDetail = "forbidden: you already have consultation session at designated time";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNoOtherOfferedConsultationRequestInConflictWithConsultationRequest(
            ConsultationRequest $consultationRequest): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('concluded', false));
        $p = function (ConsultationRequest $otherConsultationRequest) use ($consultationRequest) {
            return $otherConsultationRequest->intersectWithOtherConsultationRequest($consultationRequest) &&
                    $otherConsultationRequest->statusEquals(new ConsultationRequestStatusVO("offered"));
        };
        if (!empty($this->consultationRequests->matching($criteria)->filter($p)->count())) {
            $errorDetail = 'forbidden: you already offer designated time in other consultation request';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function findConsultationRequestOrDie(string $consultationRequestId): ConsultationRequest
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $consultationRequestId));
        $consultationRequest = $this->consultationRequests->matching($criteria)->first();
        if (empty($consultationRequest)) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
        return $consultationRequest;
    }

    public function createNotificationForConsultationSession(
            string $id, string $message, ConsultationSession $consultationSession): PersonnelNotification
    {
        return PersonnelNotification::notificationForConsultationSession(
                        $this->personnel, $id, $message, $consultationSession);
    }

    public function createNotificationForConsultationRequest(
            string $id, string $message, ConsultationRequest $consultationRequest): PersonnelNotification
    {
        return PersonnelNotification::notificationForConsultationRequest(
                        $this->personnel, $id, $message, $consultationRequest);
    }

}
