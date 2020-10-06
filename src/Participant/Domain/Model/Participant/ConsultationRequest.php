<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\Participant,
    Model\Participant\ConsultationRequest\ConsultationRequestActivityLog
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid
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
    /**
     *
     * @var ArrayCollection
     */
    protected $consultationRequestActivityLogs;

    function getId(): string
    {
        return $this->id;
    }

    function __construct(
            Participant $participant, $id, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeImmutable $startTime, ?TeamMembership $teamMemberOperator = null)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->concluded = false;
        $this->status = new ConsultationRequestStatusVO('proposed');

        $this->assertNotConflictedWithConsultantExistingConsultationSession();
        
        $this->consultationRequestActivityLogs = new ArrayCollection();
        $message = "submitted consultation request";
        $this->addConsultationRequestActivityLog($message, $teamMemberOperator);
    }

    public function isProposedConsultationRequestConflictedWith(ConsultationRequest $other): bool
    {
        if ($this->id == $other->id) {
            return false;
        }
        return $this->status->sameValueAs(new ConsultationRequestStatusVO('proposed')) && $this->startEndTime->intersectWith($other->getStartEndTime());
    }

    public function rePropose(DateTimeImmutable $startTime, ?TeamMembership $teamMemberOperator = null): void
    {
        $this->assertNotConcluded();
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->status = new ConsultationRequestStatusVO('proposed');

        $this->assertNotConflictedWithConsultantExistingConsultationSession();
        
        $this->addConsultationRequestActivityLog("changed consultation request time", $teamMemberOperator);
    }

    public function cancel(?TeamMembership $teamMemberOperator = null): void
    {
        $this->assertNotConcluded();
        $this->status = new ConsultationRequestStatusVO("cancelled");
        $this->concluded = true;
        
        $this->addConsultationRequestActivityLog("cancelled consultation request", $teamMemberOperator);
    }

    public function accept(?TeamMembership $teamMemberOperator = null): void
    {
        $this->assertNotConcluded();
        if (!$this->status->sameValueAs(new ConsultationRequestStatusVO("offered"))) {
            $errorDetail = 'forbidden: request only valid for offered consultation request';
            throw RegularException::forbidden($errorDetail);
        }

        $this->status = new ConsultationRequestStatusVO("scheduled");
        $this->concluded = true;
        
        $this->addConsultationRequestActivityLog("accepted offered consultation request", $teamMemberOperator);
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

    public function belongsTo(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

    protected function addConsultationRequestActivityLog(string $message, ?TeamMembership $teamMemberOperator): void
    {
        $message = isset($teamMemberOperator)? "team member " . $message: "participant " . $message;
        $id = Uuid::generateUuid4();
        $occuredTime = new DateTimeImmutable();
        $consultationRequestActivityLog = new ConsultationRequestActivityLog($this, $id, $message);
        
        if (isset($teamMemberOperator)) {
            $teamMemberOperator->setAsActivityOperator($consultationRequestActivityLog);
        }
        
        $this->consultationRequestActivityLogs->add($consultationRequestActivityLog);
    }

}
