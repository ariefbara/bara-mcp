<?php

namespace Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Firm\Domain\ {
    Model\Firm\Program\Metric,
    Model\Firm\Program\Participant\MetricAssignment,
    Service\MetricAssignmentDataProvider
};
use Resources\ {
    ValidationRule,
    ValidationService
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
    protected $removed;

    public function isRemoved(): bool
    {
        return $this->removed;
    }
    
    protected function setTarget(int $target)
    {
        $errorDetail = "bad request: assignment field target is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($target, $errorDetail);
        $this->target = $target;
    }

    public function __construct(MetricAssignment $metricAssignment, string $id, Metric $metric, int $target)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = $id;
        $this->metric = $metric;
        $this->setTarget($target);
        $this->removed = false;
    }

    public function update(MetricAssignmentDataProvider $metricAssignmentDataProvider): void
    {
        $target = $metricAssignmentDataProvider->pullTargetCorrespondWithMetric($this->metric);
        if (empty($target)) {
            $this->removed = true;
        } else {
            $this->setTarget($target);
        }
    }

}
