<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $consultantNoteId;

    /**
     * 
     * @var LabelData|null
     */
    protected $labelData;

    public function getConsultantNoteId(): ?string
    {
        return $this->consultantNoteId;
    }

    public function getLabelData(): ?LabelData
    {
        return $this->labelData;
    }

    public function __construct(?string $consultantNoteId, ?LabelData $labelData)
    {
        $this->consultantNoteId = $consultantNoteId;
        $this->labelData = $labelData;
    }

}
