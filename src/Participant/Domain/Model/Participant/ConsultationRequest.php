<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\ {
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\Participant
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
     * @var Participant
     */
    protected $participant;

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

    function getId(): string
    {
        return $this->id;
    }

    function __construct(
            Participant $participant, $id, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->concluded = false;
        $this->status = new ConsultationRequestStatusVO('proposed');

        $this->assertNotConflictedWithConsultantExistingConsultationSession();
    }

    public function isProposedConsultationRequestConflictedWith(ConsultationRequest $other): bool
    {
        if ($this->id == $other->id) {
            return false;
        }
        return $this->status->sameValueAs(new ConsultationRequestStatusVO('proposed')) 
                && $this->startEndTime->intersectWith($other->getStartEndTime());
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
                $this->participant, $consultationSessionId, $this->consultationSetup, $this->consultant,
                $this->startEndTime);
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
