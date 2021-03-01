<?php

namespace Participant\Domain\Model\Participant\OKRPeriod;

use Iterator;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Resources\Domain\Data\DataCollection;
use SharedContext\Domain\ValueObject\LabelData;

class ObjectiveData
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
    protected $weight;
    
    /**
     * 
     * @var DataCollection
     */
    protected $keyResultDataCollection;

    public function __construct(LabelData $labelData, ?int $weight)
    {
        $this->labelData = $labelData;
        $this->weight = $weight;
        $this->keyResultDataCollection = new DataCollection();
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }
    
    public function addKeyResultData(KeyResultData $keyResultData, ?string $keyResultId): void
    {
        $this->keyResultDataCollection->push($keyResultData, $keyResultId);
    }
    
    public function pullKeyResultData(string $keyResultId): ?KeyResultData
    {
        return $this->keyResultDataCollection->pull($keyResultId);
    }
    public function getKeyResultDataIterator(): Iterator
    {
        return $this->keyResultDataCollection;
    }

}
