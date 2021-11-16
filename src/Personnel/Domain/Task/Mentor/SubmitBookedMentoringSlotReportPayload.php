<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitBookedMentoringSlotReportPayload
{

    /**
     * 
     * @var string|null
     */
    protected $bookedMentoringSlotId;

    /**
     * 
     * @var int|null
     */
    protected $participantRating;

    /**
     * 
     * @var FormRecordData|null
     */
    protected $formRecordData;

    public function getBookedMentoringSlotId(): ?string
    {
        return $this->bookedMentoringSlotId;
    }

    public function getParticipantRating(): ?int
    {
        return $this->participantRating;
    }

    public function getFormRecordData(): ?FormRecordData
    {
        return $this->formRecordData;
    }

    public function __construct(?string $bookedMentoringSlotId, ?int $participantRating, ?FormRecordData $formRecordData)
    {
        $this->bookedMentoringSlotId = $bookedMentoringSlotId;
        $this->participantRating = $participantRating;
        $this->formRecordData = $formRecordData;
    }

}
