<?php

namespace Firm\Domain\Service;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program\Metric;
use SplObjectStorage;

class MetricAssignmentDataProvider
{

    /**
     *
     * @var MetricRepository
     */
    protected $metricRepositoy;

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
     * @var SplObjectStorage
     */
    protected $collection;

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function __construct(
            MetricRepository $metricRepositoy, ?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate)
    {
        $this->metricRepositoy = $metricRepositoy;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->collection = new \SplObjectStorage();
    }

    public function add(string $metricId, ?int $target): void
    {
        $metric = $this->metricRepositoy->ofId($metricId);
        $this->collection->attach($metric, $target);
    }

    public function pullTargetCorrespondWithMetric(Metric $metric): ?int
    {
        $target = null;
        if ($this->collection->contains($metric)) {
            $target = $this->collection[$metric];
            $this->collection->detach($metric);
        }
        return $target;
    }

    /**
     * 
     * @return Metric[]
     */
    public function iterateMetrics(): array
    {
        $metrics = [];
        foreach ($this->collection as $metric) {
            $metrics[] = $metric;
        }
        return $metrics;
    }

}
