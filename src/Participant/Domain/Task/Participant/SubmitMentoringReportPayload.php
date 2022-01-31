<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;

class SubmitMentoringReportPayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $mentorRating;

    /**
     * 
     * @var FormRecordData|null
     */
    protected $formRecordData;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getMentorRating(): ?int
    {
        return $this->mentorRating;
    }

    public function getFormRecordData(): ?FormRecordData
    {
        return $this->formRecordData;
    }

    public function __construct(?string $id, ?int $mentorRating, ?FormRecordData $formRecordData)
    {
        $this->id = $id;
        $this->mentorRating = $mentorRating;
        $this->formRecordData = $formRecordData;
    }

}
