<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\{
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\ConsultationRequest\ConsultationRequestActivityLog
};
use Resources\{
    Domain\Event\CommonEvent,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\ConsultationRequestStatusVO;

class ConsultationRequest extends EntityContainEvents implements AssetBelongsToTeamInterface
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
            DateTimeImmutable $startTime, ?TeamMembership $teamMember)
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
        $this->addConsultationRequestActivityLog($message, $teamMember);

        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_SUBMITTED, $this->id);
        $this->recordEvent($event);
    }

    function scheduleIntersectWith(DateTimeInterval $startEndTime): bool
    {
        return $this->startEndTime->intersectWith($startEndTime);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->participant->belongsToTeam($team);
    }

    public function isProposedConsultationRequestConflictedWith(ConsultationRequest $other): bool
    {
        if ($this->id == $other->id) {
            return false;
        }
        return $this->status->sameValueAs(new ConsultationRequestStatusVO('proposed')) && $this->startEndTime->intersectWith($other->startEndTime);
    }

    public function rePropose(DateTimeImmutable $startTime, ?TeamMembership $teamMember): void
    {
        $this->assertNotConcluded();
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
        $this->status = new ConsultationRequestStatusVO('proposed');

        $this->assertNotConflictedWithConsultantExistingConsultationSession();

        $this->addConsultationRequestActivityLog("changed consultation request time", $teamMember);

        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_TIME_CHANGED, $this->id);
        $this->recordEvent($event);
    }

    public function cancel(?TeamMembership $teamMember = null): void
    {
        $this->assertNotConcluded();
        $this->status = new ConsultationRequestStatusVO("cancelled");
        $this->concluded = true;

        $this->addConsultationRequestActivityLog("cancelled consultation request", $teamMember);

        $event = new CommonEvent(EventList::CONSULTATION_REQUEST_CANCELLED, $this->id);
        $this->recordEvent($event);
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

    public function createConsultationSession(string $consultationSessionId, ?TeamMembership $teamMember): ConsultationSession
    {
        return new ConsultationSession(
                $this->participant, $consultationSessionId, $this->consultationSetup, $this->consultant,
                $this->startEndTime, $teamMember);
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
        if (!$this->consultant->canAcceptConsultationRequest($this)) {
            $errorDetail = "forbidden: consultant can accept consultation request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function addConsultationRequestActivityLog(string $message, ?TeamMembership $teamMember): void
    {
        $message = isset($teamMember) ? "team member " . $message : "participant " . $message;
        $id = Uuid::generateUuid4();
        $consultationRequestActivityLog = new ConsultationRequestActivityLog($this, $id, $message, $teamMember);
        $this->consultationRequestActivityLogs->add($consultationRequestActivityLog);
    }

}
