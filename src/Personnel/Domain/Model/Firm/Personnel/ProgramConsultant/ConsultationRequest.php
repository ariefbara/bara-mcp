<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException
};
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;

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

    function getId(): string
    {
        return $this->id;
    }

    function isConcluded(): bool
    {
        return $this->concluded;
    }

    function getStartEndTime(): DateTimeInterval
    {
        return $this->startEndTime;
    }

    protected function __construct()
    {
        ;
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
        return $this->status->sameValueAs(new ConsultationRequestStatusVO('offered'))
            && $this->startEndTime->intersectWith($other->getStartEndTime());
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

}
