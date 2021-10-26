<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest\ConsultationRequestActivityLog;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;

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
     * @var ConsultationChannel
     */
    protected $channel;

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

    protected function setStartEndTime(?DateTimeImmutable $startTime): void
    {
        $errorDetail = "bad request: consultation request start time is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($startTime, $errorDetail);
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
    }

    protected function __construct()
    {
        
    }

    public function scheduleIntersectWith(DateTimeInterval $otherSchedule): bool
    {
        return !$this->concluded && $this->startEndTime->intersectWith($otherSchedule);
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

    public function offer(ConsultationRequestData $consultationRequestData): void
    {
        $this->setStartEndTime($consultationRequestData->getStartTime());
        $this->channel = new ConsultationChannel(
                $consultationRequestData->getMedia(), $consultationRequestData->getAddress());

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
        $sessionType = new ConsultationSessionType(ConsultationSessionType::HANDSHAKING_TYPE, null);
        return new ConsultationSession(
                $this->programConsultant, $consultationSessionId, $this->participant, $this->consultationSetup,
                $this->startEndTime, $this->channel, $sessionType);
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
