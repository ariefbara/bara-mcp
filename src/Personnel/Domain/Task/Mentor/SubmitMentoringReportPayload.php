<?php

namespace Personnel\Domain\Task\Mentor;

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
    protected $participantRating;

    /**
     * 
     * @var FormRecordData|null
     */
    protected $formRecordData;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getParticipantRating(): ?int
    {
        return $this->participantRating;
    }

    public function getFormRecordData(): ?FormRecordData
    {
        return $this->formRecordData;
    }

    public function __construct(?string $id, ?int $participantRating, ?FormRecordData $formRecordData)
    {
        $this->id = $id;
        $this->participantRating = $participantRating;
        $this->formRecordData = $formRecordData;
    }

}
