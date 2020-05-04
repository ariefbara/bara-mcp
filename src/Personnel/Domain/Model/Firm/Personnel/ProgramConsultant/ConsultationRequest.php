<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ {
    Personnel\PersonnelNotification,
    Personnel\ProgramConsultant,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException
};
use Shared\Domain\Model\ConsultationRequestStatusVO;

class ConsultationRequest
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

    function getProgramConsultant(): ProgramConsultant
    {
        return $this->programConsultant;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    function getStartTimeString(): string
    {
        return $this->startEndTime->getStartTime()->format('Y-m-d H:i:s');
    }

    function getEndTimeString(): string
    {
        return $this->startEndTime->getEndTime()->format('Y-m-d H:i:s');
    }

    function isConcluded(): bool
    {
        return $this->concluded;
    }

    function getStatusString(): string
    {
        return $this->status->getValue();
    }

    protected function __construct()
    {
        ;
    }

    function getStartEndTime(): DateTimeInterval
    {
        return $this->startEndTime;
    }

    public function reject(): void
    {
        $this->assertNotConcluded();
        $this->status = new ConsultationRequestStatusVO("rejected");
        $this->concluded = true;
    }

    public function offer(DateTimeImmutable $startTime): void
    {
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);

        $this->assertNotConcluded();
        $this->assertNotConflictWithParticipantMentoringSchedule();
        $this->status = new ConsultationRequestStatusVO('offered');
    }

    public function accept(): void
    {
        $this->assertNotConcluded();
        if (!$this->statusEquals(new ConsultationRequestStatusVO('proposed'))) {
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

    public function intersectWithOtherConsultationRequest(ConsultationRequest $other): bool
    {
        if ($this->id == $other->id) {
            return false;
        }
        return $this->startEndTime->intersectWith($other->getStartEndTime());
    }

    public function statusEquals(ConsultationRequestStatusVO $other): bool
    {
        return $this->status->sameValueAs($other);
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
    
    public function createNotification(string $id, string $message): PersonnelNotification
    {
        return $this->programConsultant->createNotificationForConsultationRequest($id, $message, $this);
    }

}
