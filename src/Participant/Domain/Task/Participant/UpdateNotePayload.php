<?php

namespace Participant\Domain\Task\Participant;

use SharedContext\Domain\ValueObject\LabelData;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var LabelData
     */
    protected $labelData;

    public function __construct(?string $id, LabelData $labelData)
    {
        $this->id = $id;
        $this->labelData = $labelData;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

}
