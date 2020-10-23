<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Metric,
    Model\Participant\MetricAssignment
};
use Resources\Exception\RegularException;

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

    protected function __construct()
    {
        
    }

    public function setValueIn(MetricAssignmentReport $metricAssignmentReport,
            MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $value = $metricAssignmentReportData->getValueCorrespondWithAssignmentField($this->id);
        if (!$this->metric->isValueAcceptable($value)) {
            $errorDetail = "forbidden: value is out of bound";
            throw RegularException::forbidden($errorDetail);
        }
        
        $metricAssignmentReport->setAssignmentFieldValue($this, $value);
    }

}
