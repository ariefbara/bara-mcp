<?php

namespace Personnel\Domain\Task\Coordinator;

use SharedContext\Domain\ValueObject\LabelData;

class SubmitNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    /**
     * 
     * @var LabelData|null
     */
    protected $labelData;

    /**
     * 
     * @var bool|null
     */
    protected $viewableByParticipant;
    public $submittedNoteId;

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function getLabelData(): ?LabelData
    {
        return $this->labelData;
    }

    public function getViewableByParticipant(): ?bool
    {
        return $this->viewableByParticipant;
    }

    public function __construct(?string $participantId, ?LabelData $labelData, ?bool $viewableByParticipant)
    {
        $this->participantId = $participantId;
        $this->labelData = $labelData;
        $this->viewableByParticipant = $viewableByParticipant;
    }

}
