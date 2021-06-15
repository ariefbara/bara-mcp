<?php

namespace Query\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Query\Domain\Model\Firm\Program\{
    Metric,
    Participant\MetricAssignment
};

class AssignmentField
{

    /**
     *
     * @var MetricAssignment
     */
    protected $metricAssignment;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Metric
     */
    protected $metric;

    /**
     *
     * @var int
     */
    protected $target;

    /**
     *
     * @var bool
     */
    protected $disabled;

    public function getMetricAssignment(): MetricAssignment
    {
        return $this->metricAssignment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMetric(): Metric
    {
        return $this->metric;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

}
