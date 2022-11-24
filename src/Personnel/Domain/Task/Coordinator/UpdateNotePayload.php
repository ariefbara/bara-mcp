<?php

namespace Personnel\Domain\Task\Coordinator;

use SharedContext\Domain\ValueObject\LabelData;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $coordinatorNoteId;

    /**
     * 
     * @var string|null
     */
    protected $labelData;

    public function getCoordinatorNoteId(): ?string
    {
        return $this->coordinatorNoteId;
    }

    public function getLabelData(): ?LabelData
    {
        return $this->labelData;
    }

    public function __construct(?string $coordinatorNoteId, ?LabelData $labelData)
    {
        $this->coordinatorNoteId = $coordinatorNoteId;
        $this->labelData = $labelData;
    }

}
