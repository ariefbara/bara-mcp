<?php

namespace Participant\Domain\Model\Participant\ConsultationSession;

use Participant\Domain\Model\Participant\ConsultationSession;
use Resources\ValidationRule;
use Resources\ValidationService;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class ParticipantFeedback
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
    protected $mentorRating;

    public function setMentorRating(?int $mentorRating)
    {
        $errorDetail = "bad request: mentor rating must be betwenn 1-5";
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::between(1, 5)))
                ->execute($mentorRating, $errorDetail);
        $this->mentorRating = $mentorRating;
    }

    function __construct(
            ConsultationSession $consultationSession, string $id, FormRecord $formRecord, ?int $mentorRating)
    {
        $this->consultationSession = $consultationSession;
        $this->id = $id;
        $this->formRecord = $formRecord;
        $this->setMentorRating($mentorRating);
    }

    public function update(FormRecordData $formRecordData, ?int $mentorRating): void
    {
        $this->formRecord->update($formRecordData);
        $this->setMentorRating($mentorRating);
    }

}
