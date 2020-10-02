<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Consultant,
    DependencyModel\Firm\Program\ConsultationSetup,
    Model\AssetBelongsToParticipantInterface,
    Model\Participant,
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

    function __construct(
            Participant $participant, $id, ConsultationSetup $consultationSetup,
            Consultant $consultant, DateTimeInterval $startEndTime)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->consultationSetup = $consultationSetup;
        $this->consultant = $consultant;
        $this->startEndTime = $startEndTime;
    }

    public function conflictedWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return $this->startEndTime->intersectWith($consultationRequest->getStartEndTime());
    }

    public function setParticipantFeedback(FormRecordData $formRecordData): void
    {
        if (!empty($this->participantFeedback)) {
            $this->participantFeedback->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord);
        }
    }

    public function belongsTo(Participant $participant): bool
    {
        return $this->participant === $participant;
    }

}
