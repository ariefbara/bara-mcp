<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use SharedContext\Domain\ValueObject\LabelData;

class TaskData
{

    /**
     * 
     * @var LabelData
     */
    protected $labelData;
    protected $dueDate;

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

    public function getDueDate()
    {
        return $this->dueDate;
    }

    public function __construct(LabelData $labelData, $dueDate)
    {
        $this->labelData = $labelData;
        $this->dueDate = $dueDate;
    }

}
