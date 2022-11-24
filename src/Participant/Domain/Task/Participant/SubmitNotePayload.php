<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\LabelData;

class SubmitNotePayload
{

    /**
     * 
     * @var LabelData
     */
    protected $labelData;
    public $submittedNoteId;

    public function __construct(LabelData $labelData)
    {
        $this->labelData = $labelData;
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

}
