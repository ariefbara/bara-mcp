<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use DateTimeImmutable;
use Iterator;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Resources\Domain\Data\DataCollection;

class ObjectiveProgressReportData
{

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $reportDate;

    /**
     * 
     * @var DataCollection
     */
    protected $keyResultProgressReportDataCollection;

    public function __construct(?DateTimeImmutable $reportDate)
    {
        $this->reportDate = $reportDate;
        $this->keyResultProgressReportDataCollection = new DataCollection();
    }

    public function getReportDate(): ?DateTimeImmutable
    {
        return $this->reportDate;
    }

    public function addKeyResultProgressReportData(KeyResultProgressReportData $keyResultProgressReportData, string $keyResultId): void
    {
        $this->keyResultProgressReportDataCollection->push($keyResultProgressReportData, $keyResultId);
    }

    public function pullKeyResultProgressReportData(string $keyResultId): ?KeyResultProgressReportData
    {
        return $this->keyResultProgressReportDataCollection->pull($keyResultId);
    }

    /**
     * 
     * @return KeyResultProgressReportData[]
     */
    public function getKeyResultProgressReportDataIterator(): Iterator
    {
        return $this->keyResultProgressReportDataCollection;
    }

}
