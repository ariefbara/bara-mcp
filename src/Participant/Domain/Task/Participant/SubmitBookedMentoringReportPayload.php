<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitBookedMentoringReportPayload
{

    /**
     * 
     * @var string
     */
    protected $bookedMentoringSlotId;

    /**
     * 
     * @var int
     */
    protected $mentorRating;

    /**
     * 
     * @var FormRecordData
     */
    protected $formRecordData;

    public function getBookedMentoringSlotId(): string
    {
        return $this->bookedMentoringSlotId;
    }

    public function getMentorRating(): int
    {
        return $this->mentorRating;
    }

    public function getFormRecordData(): FormRecordData
    {
        return $this->formRecordData;
    }

    public function __construct(string $bookedMentoringSlotId, int $mentorRating, FormRecordData $formRecordData)
    {
        $this->bookedMentoringSlotId = $bookedMentoringSlotId;
        $this->mentorRating = $mentorRating;
        $this->formRecordData = $formRecordData;
    }

}
