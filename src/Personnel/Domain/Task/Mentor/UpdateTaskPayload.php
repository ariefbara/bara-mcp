<?php

namespace Personnel\Domain\Task\Mentor;

use SharedContext\Domain\ValueObject\LabelData;

class UpdateTaskPayload
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var LabelData
     */
    protected $labelData;

    public function __construct(string $id, LabelData $labelData)
    {
        $this->id = $id;
        $this->labelData = $labelData;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

}
