<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Iterator;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Resources\Domain\Data\DataCollection;
use SharedContext\Domain\ValueObject\LabelData;

class OKRPeriodData
{

    /**
     * 
     * @var LabelData
     */
    protected $labelData;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $startDate;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $endDate;

    /**
     * 
     * @var DataCollection
     */
    protected $objectiveDataCollection;

    public function __construct(LabelData $labelData, ?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate)
    {
        $this->labelData = $labelData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->objectiveDataCollection = new DataCollection();
    }

    public function getLabelData(): LabelData
    {
        return $this->labelData;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getObjectiveDataList(): DataCollection
    {
        return $this->objectiveDataCollection;
    }

    public function addObjectiveData(ObjectiveData $objectiveData, ?string $objectiveId): void
    {
        $this->objectiveDataCollection->push($objectiveData, $objectiveId);
    }

    public function pullObjectiveDataWithId(string $objectiveId): ?ObjectiveData
    {
        return $this->objectiveDataCollection->pull($objectiveId);
    }

    public function getObjectiveDataCollectionIterator(): Iterator
    {
        return $this->objectiveDataCollection;
    }

}
