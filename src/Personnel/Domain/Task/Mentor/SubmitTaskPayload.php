<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;

class SubmitTaskPayload
{

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var LabelData
     */
    protected $labelData;
    public $submittedTaskId;

    public function __construct(string $participantId, LabelData $labelData)
    {
        $this->participantId = $participantId;
        $this->labelData = $labelData;
    }

    public function getParticipantId(): string
    {
        return $this->participantId;
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

}
