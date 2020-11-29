<?php

namespace Participant\Domain\Model\Participant;

use Config\EventList;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\ConsultationSession\ConsultationSessionActivityLog,
    Model\Participant\ConsultationSession\ParticipantFeedback
};
use Resources\ {
    Domain\Event\CommonEvent,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\DateTimeInterval,
    Exception\RegularException,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

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
            DateTimeInterval $startEndTime, ?TeamMembership $teamMember)
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

    public function setParticipantFeedback(FormRecordData $formRecordData, ?TeamMembership $teamMember = null): void
    {
        if ($this->cancelled) {
            $errorDetail = "forbidden: can send report on cancelled session";
            throw RegularException::forbidden($errorDetail);
        }
        if (!empty($this->participantFeedback)) {
            $this->participantFeedback->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord);
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

}
