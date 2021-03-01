<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use SharedContext\Domain\ValueObject\LabelData;

class KeyResultData
{

    /**
     * 
     * @var LabelData
     */
    protected $labelData;

    /**
     * 
     * @var int|null
     */
    protected $target;

    /**
     * 
     * @var int|null
     */
    protected $weight;

    public function __construct(LabelData $labelData, ?int $target, ?int $weight)
    {
        $this->labelData = $labelData;
        $this->target = $target;
        $this->weight = $weight;
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

    public function getTarget(): ?int
    {
        return $this->target;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

}
