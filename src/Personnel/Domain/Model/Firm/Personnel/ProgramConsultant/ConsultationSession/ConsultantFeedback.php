<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationSession;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ConsultantFeedback
{

    /**
     *
     * @var ConsultationSession
     */
    protected $consultationSession;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FormRecord
     */
    protected $formRecord;

    /**
     * 
     * @var int|null
     */
    protected $participantRating;

    public function setParticipantRating(?int $participantRating)
    {
        $errorDetail = "bad request: participant rating must be between 1-5";
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::between(1, 5)))
                ->execute($participantRating, $errorDetail);
        $this->participantRating = $participantRating;
    }

    function __construct(
            ConsultationSession $consultationSession, string $id, FormRecord $formRecord, ?int $participantRating)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->formRecord = $formRecord;
        $this->setParticipantRating($participantRating);
    }

    public function update(FormRecordData $formRecordData, ?int $participantRating): void
    {
        $this->formRecord->update($formRecordData);
        $this->setParticipantRating($participantRating);
    }

}
