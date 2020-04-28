<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\{
    Client\ProgramParticipation,
    Client\ProgramParticipation\ConsultationSession\ParticipantFeedback,
    Firm\Program\Consultant,
    Firm\Program\ConsultationSetup
};
use Resources\Domain\ValueObject\DateTimeInterval;
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

    function getParticipantFeedback(): ?ParticipantFeedback
    {
        return $this->participantFeedback;
    }

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
            $id = \Resources\Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordFormParticipantFeedback($id, $formRecordData);
            $this->participantFeedback = new ParticipantFeedback($this, $id, $formRecord);
        }
    }

}
