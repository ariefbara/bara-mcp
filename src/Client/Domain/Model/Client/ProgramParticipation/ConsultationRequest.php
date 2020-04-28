<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\ {
    Client\ProgramParticipation,
    Firm\Program\Consultant,
    Firm\Program\ConsultationSetup
};
use DateTimeImmutable;
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException
};
use Shared\Domain\Model\ConsultationRequestStatusVO;

class ConsultationRequest
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    function getProgramParticipation(): ProgramParticipation
    {
        return $this->programParticipation;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getConsultationSetup(): ConsultationSetup
    {
        return $this->consultationSetup;
    }

    function getConsultant(): Consultant
    {
        return $this->consultant;
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

    function __construct(
            ProgramParticipation $programParticipation, $id, ConsultationSetup $consultationSetup,
            Consultant $consultant, DateTimeImmutable $startTime)
    {
        $this->programParticipation = $programParticipation;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->concluded = false;
        $this->status = new ConsultationRequestStatusVO('proposed');

        $this->assertNotConflictedWithConsultantExistingConsultationSession();
    }

    public function rePropose(DateTimeImmutable $startTime): void
    {
        $this->assertNotConcluded();
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->status = new ConsultationRequestStatusVO('proposed');
        
        $this->assertNotConflictedWithConsultantExistingConsultationSession();
    }

    public function cancel(): void
    {
        $this->assertNotConcluded();
        $this->status = new ConsultationRequestStatusVO("cancelled");
        $this->concluded = true;
    }

    public function accept(): void
    {
        $this->assertNotConcluded();
        if (!$this->status->sameValueAs(new ConsultationRequestStatusVO("offered"))) {
            $errorDetail = 'forbidden: request only valid for offered consultation request';
            throw RegularException::forbidden($errorDetail);
        }

        $this->status = new ConsultationRequestStatusVO("scheduled");
        $this->concluded = true;
    }

    public function createConsultationSession(string $consultationSessionId): ConsultationSession
    {
        return new ConsultationSession(
                $this->programParticipation, $consultationSessionId, $this->consultationSetup, $this->consultant,
                $this->startEndTime);
    }

    public function statusEquals(ConsultationRequestStatusVO $status): bool
    {
        return $this->status->sameValueAs($status);
    }

    public function conflictedWith(ConsultationRequest $other): bool
    {
        if ($this->id == $other->getId()) {
            return false;
        }
        return $this->startEndTime->intersectWith($other->getStartEndTime());
    }

    function getStartEndTime(): DateTimeInterval
    {
        return $this->startEndTime;
    }

    protected function assertNotConcluded(): void
    {
        if ($this->concluded) {
            $errorDetail = 'forbidden: consultation request already concluded';
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertNotConflictedWithConsultantExistingConsultationSession(): void
    {
        if ($this->consultant->hasConsultationSessionConflictedWith($this)) {
            $errorDetail = "conflict: consultant already has consultation session at this time";
            throw RegularException::conflict($errorDetail);
        }
    }

}
