<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Personnel\ProgramConsultant\ConsultationRequest\ConsultationRequestActivityLog,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\ {
    Domain\Event\CommonEvent,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;

class ConsultationRequest extends EntityContainEvents
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var bool
     */
    protected $concluded;

    /**
     *
     * @var ConsultationRequestStatusVO
     */
    protected $status;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestActivityLogs;

    function getId(): string
    {
        return $this->id;
    }

    function isConcluded(): bool
    {
        return $this->concluded;
    }
//
//    function getStartEndTime(): DateTimeInterval
//    {
//        return $this->startEndTime;
//    }

    protected function __construct()
    {
        ;
    }

    public function reject(): void
    {
        $this->assertNotConcluded();
        $this->status = new ConsultationRequestStatusVO("rejected");
        $this->concluded = true;

        $this->logActivity("consultation request rejected");
        
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_REJECTED, $this->id);
        $this->recordEvent($event);
    }

    public function offer(DateTimeImmutable $startTime): void
    {
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);

        $this->assertNotConcluded();
        $this->assertNotConflictWithParticipantMentoringSchedule();
        $this->status = new ConsultationRequestStatusVO('offered');
        
        $this->logActivity("consultation request new schedule offered");
        
        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_OFFERED, $this->id);
        $this->recordEvent($event);
    }

    public function accept(): void
    {
        $this->assertNotConcluded();
        if (!$this->status->sameValueAs(new ConsultationRequestStatusVO('proposed'))) {
            $errorDetail = "forbidden: can only accept proposed consultation request";
            throw RegularException::forbidden($errorDetail);
        }
        $this->status = new ConsultationRequestStatusVO('scheduled');
        $this->concluded = true;
    }

    public function createConsultationSession(string $consultationSessionId): ConsultationSession
    {
        return new ConsultationSession(
                $this->programConsultant, $consultationSessionId, $this->participant, $this->consultationSetup,
                $this->startEndTime);
    }

    public function isOfferedConsultationRequestConflictedWith(ConsultationRequest $other): bool
    {
        if ($this->id === $other->id) {
            return false;
        }
        return $this->status->sameValueAs(new ConsultationRequestStatusVO('offered')) && $this->startEndTime->intersectWith($other->startEndTime);
    }

    protected function assertNotConcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = 'forbidden: consultation request already concluded';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNotConflictWithParticipantMentoringSchedule(): void
    {
        if ($this->participant->hasConsultationSessionInConflictWithConsultationRequest($this)) {
            $errorDetail = 'forbidden: consultation request time in conflict with participan existing consultation session';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function logActivity(string $message): void
    {
        $id = Uuid::generateUuid4();
        $consultationRequestActivityLog = new ConsultationRequestActivityLog(
                $this, $id, $message, $this->programConsultant);
        $this->consultationRequestActivityLogs->add($consultationRequestActivityLog);
    }

}
