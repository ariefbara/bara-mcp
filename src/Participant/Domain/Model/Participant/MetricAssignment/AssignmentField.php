<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Metric,
    Model\Participant\MetricAssignment,
    Service\MetricAssignmentReportDataProvider
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
    protected $disabled;

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

    public function setValueIn(
            MetricAssignmentReport $metricAssignmentReport,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        $assignmentFieldValueData = $metricAssignmentReportDataProvider
                ->getAssignmentFieldValueDataCorrespondWithAssignmentField($this->id);
        if (!$this->metric->isValueAcceptable($assignmentFieldValueData->getValue())) {
            $errorDetail = "forbidden: value is out of bound";
            throw RegularException::forbidden($errorDetail);
        }

        $metricAssignmentReport->setAssignmentFieldValue($this, $assignmentFieldValueData);
    }

}
