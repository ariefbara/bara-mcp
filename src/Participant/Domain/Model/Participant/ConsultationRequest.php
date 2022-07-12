<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationRequest\ConsultationRequestActivityLog;
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

    protected function setStartEndTime(?DateTimeImmutable $startTime): void
    {
        $errorDetail = "bad request: consultation request start time is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($startTime, $errorDetail);
        $this->startEndTime = $this->consultationSetup->getSessionStartEndTimeOf($startTime);
    }

    function __construct(
            Participant $participant, $id, ConsultationSetup $consultationSetup, Consultant $consultant,
            ConsultationRequestData $consultationRequestData, ?TeamMembership $teamMember)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->setStartEndTime($consultationRequestData->getStartTime());
        $this->channel = new ConsultationChannel(
                $consultationRequestData->getMedia(), $consultationRequestData->getAddress());
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

    public function rePropose(ConsultationRequestData $consultationRequestData, ?TeamMembership $teamMember): void
    {
        $this->assertNotConcluded();
        $this->setStartEndTime($consultationRequestData->getStartTime());
        $this->status = new ConsultationRequestStatusVO('proposed');
        $this->channel = new ConsultationChannel(
                $consultationRequestData->getMedia(), $consultationRequestData->getAddress());

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
        $consultationSessionType = new ConsultationSessionType(ConsultationSessionType::HANDSHAKING_TYPE);
        return new ConsultationSession(
                $this->participant, $consultationSessionId, $this->consultationSetup, $this->consultant,
                $this->startEndTime, $this->channel, $consultationSessionType, $teamMember);
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
