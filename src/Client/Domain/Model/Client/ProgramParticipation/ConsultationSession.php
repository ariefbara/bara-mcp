<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\ {
    Client\ClientNotification,
    Client\ProgramParticipation,
    Client\ProgramParticipation\ConsultationSession\ParticipantFeedback,
    Firm\Program\Consultant,
    Firm\Program\ConsultationSetup
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use Shared\Domain\Model\FormRecordData;

class ConsultationSession
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
     * @var ParticipantFeedback
     */
    protected $participantFeedback = null;

    function __construct(
            ProgramParticipation $programParticipation, $id, ConsultationSetup $consultationSetup,
            Consultant $consultant, DateTimeInterval $startEndTime)
    {
        $this->consultationSetup = $consultationSetup;
        $this->id = $id;
        $this->programParticipation = $programParticipation;
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
            $formRecord = $this->consultationSetup->createFormRecordFormParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord);
        }
    }
    
    public function createClientNotification(string $id, string $message): ClientNotification
    {
        return $this->programParticipation->createNotificationForConsultationSession($id, $message, $this);
    }
}
