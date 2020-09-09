<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\ {
    Personnel\ProgramConsultant,
    Personnel\ProgramConsultant\ConsultationSession\ConsultantFeedback,
    Program\ConsultationSetup,
    Program\Participant
};
use Resources\ {
    Domain\ValueObject\DateTimeInterval,
    Uuid
};
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ConsultationSession
{

    /**
     *
     * @var ProgramConsultant
     */
    protected $programConsultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var ConsultationSetup
     */
    protected $consultationSetup;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

    /**
     *
     * @var ConsultantFeedback
     */
    protected $consultantFeedback = null;

    function __construct(
            ProgramConsultant $programConsultant, string $id, Participant $participant,
            ConsultationSetup $consultationSetup, DateTimeInterval $startEndTime)
    {
        $this->programConsultant = $programConsultant;
        $this->id = $id;
        $this->participant = $participant;
        $this->consultationSetup = $consultationSetup;
        $this->startEndTime = $startEndTime;
    }

    public function intersectWithConsultationRequest(ConsultationRequest $consultationRequest): bool
    {
        return $this->startEndTime->intersectWith($consultationRequest->getStartEndTime());
    }

    public function setConsultantFeedback(FormRecordData $formRecordData): void
    {
        if (!empty($this->consultantFeedback)) {
            $this->consultantFeedback->update($formRecordData);
        } else {
            $id = Uuid::generateUuid4();
            $formRecord = $this->consultationSetup->createFormRecordForConsultantFeedback($id, $formRecordData);
            $this->consultantFeedback = new ConsultantFeedback($this, $id, $formRecord);
        }
    }

}
