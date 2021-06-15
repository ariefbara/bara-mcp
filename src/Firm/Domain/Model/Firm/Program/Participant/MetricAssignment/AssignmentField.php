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
    protected $disabled;

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
        $this->disabled = false;
    }

    public function update(MetricAssignmentDataProvider $metricAssignmentDataProvider): void
    {
        $target = $metricAssignmentDataProvider->pullTargetCorrespondWithMetric($this->metric);
        if (empty($target)) {
            $this->disabled = true;
        } else {
            $this->setTarget($target);
            $this->disabled = false;
        }
    }

}
