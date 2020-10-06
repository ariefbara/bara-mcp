<?php

namespace Participant\Domain\Model\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Client\TeamMembership,
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\AssetBelongsToParticipantInterface,
    Model\Participant,
    Model\Participant\ConsultationSession\ConsultationSessionActivityLog,
    Model\Participant\ConsultationSession\ParticipantFeedback
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ConsultationSession implements AssetBelongsToParticipantInterface
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
            DateTimeInterval $startEndTime)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $startEndTime;
        
        $this->consultationSessionActivityLogs = new ArrayCollection();
    }

    public function conflictedWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return $this->startEndTime->intersectWith($consultationRequest->getStartEndTime());
    }

    public function setParticipantFeedback(FormRecordData $formRecordData, ?TeamMembership $teamMember = null): void
    {
        if (!empty($this->participantFeedback)) {
            $this->participantFeedback->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord);
        }
        $this->addActivityLog("submitted consultation report", $teamMember);
    }

    public function belongsTo(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

    protected function addActivityLog(string $message, ?TeamMembership $teamMember): void
    {
        $messageWithActor = isset($teamMember)? "team member $message": "participant $message";
        $id = Uuid::generateUuid4();
        $consultationSesssionActivityLog = new ConsultationSessionActivityLog($this, $id, $messageWithActor);
        
        if (isset($teamMember)) {
            $teamMember->setAsActivityOperator($consultationSesssionActivityLog);
        }
        
        $this->consultationSessionActivityLogs->add($consultationSesssionActivityLog);
    }

}
