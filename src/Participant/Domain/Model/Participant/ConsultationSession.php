<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Client\AssetBelongsToTeamInterface;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\DependencyModel\Firm\Team;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\ConsultationSession\ConsultationSessionActivityLog;
use Participant\Domain\Model\Participant\ConsultationSession\ParticipantFeedback;
use Resources\Domain\Event\CommonEvent;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Domain\ValueObject\DateTimeInterval;
use Resources\Exception\RegularException;
use Resources\Uuid;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\ConsultationChannel;
use SharedContext\Domain\ValueObject\ConsultationSessionType;

class ConsultationSession extends EntityContainEvents implements AssetBelongsToTeamInterface
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
     * @var ConsultationSessionType
     */
    protected $sessionType;

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var ParticipantFeedback
     */
    protected $participantFeedback = null;

    /**
     *
     * @var ArrayCollection
     */
    protected $consultationSessionActivityLogs;

    function __construct(
            Participant $participant, $id, ConsultationSetup $consultationSetup, Consultant $consultant,
            DateTimeInterval $startEndTime, ConsultationChannel $channel, ConsultationSessionType $sessionType,
            ?TeamMembership $teamMember = null)
    {
        if (!$consultant->isActive()) {
            $errorDetail = "forbidden: inactive mentor can't give consultation";
            throw RegularException::forbidden($errorDetail);
        }
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $startEndTime;
        $this->channel = $channel;
        $this->sessionType = $sessionType;
        $this->cancelled = false;

        $this->consultationSessionActivityLogs = new ArrayCollection();

        $this->addActivityLog("scheduled consultation session", $teamMember);

        $event = new CommonEvent(EventList::OFFERED_CONSULTATION_REQUEST_ACCEPTED, $this->id);
        $this->recordEvent($event);
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->participant->belongsToTeam($team);
    }

    public function conflictedWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return !$this->cancelled && $consultationRequest->scheduleIntersectWith($this->startEndTime);
    }

    public function setParticipantFeedback(
            FormRecordData $formRecordData, ?int $mentorRating, ?TeamMembership $teamMember = null): void
    {
        if ($this->cancelled) {
            $errorDetail = "forbidden: can send report on cancelled session";
            throw RegularException::forbidden($errorDetail);
        }
        if (!empty($this->participantFeedback)) {
            $this->participantFeedback->update($formRecordData, $mentorRating);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord, $mentorRating);
        }
        $this->addActivityLog("submitted consultation report", $teamMember);
    }

    protected function addActivityLog(string $message, ?TeamMembership $teamMember): void
    {
        $message = isset($teamMember) ? "team member $message" : "participant $message";
        $id = Uuid::generateUuid4();
        $consultationSesssionActivityLog = new ConsultationSessionActivityLog($this, $id, $message, $teamMember);
        $this->consultationSessionActivityLogs->add($consultationSesssionActivityLog);
    }
    
    public function cancel(): void
    {
        if (!$this->sessionType->canBeCancelled() || $this->cancelled) {
            throw RegularException::forbidden('forbidden: unable to cancel session, either uncancellable (non declared type) or already cancelled');
        }
        $this->cancelled = true;
    }
    
    public function assertManageableByParticipant(Participant $participant): void
    {
        if ($this->participant !== $participant || $this->cancelled) {
            $errorDetail = 'forbidden: unmanaged consultation session, either inactive session or belongs to different participant';
            throw RegularException::forbidden($errorDetail);
        }
    }

}
