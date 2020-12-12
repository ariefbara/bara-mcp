<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\{
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Team,
    Model\Participant\MetricAssignment,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValueData,
    Service\MetricAssignmentReportDataProvider
};
use Resources\{
    DateTimeImmutableBuilder,
    Exception\RegularException,
    Uuid
};

class MetricAssignmentReport implements AssetBelongsToTeamInterface
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
     * @var DateTimeImmutable
     */
    protected $observationTime;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;
    
    /**
     *
     * @var bool|null
     */
    protected $approved;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ArrayCollection
     */
    protected $assignmentFieldValues;

    public function __construct(
            MetricAssignment $metricAssignment, string $id, DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = $id;
        $this->observationTime = $observationTime;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;
        $this->approved = null;

        $this->assignmentFieldValues = new ArrayCollection();
        $this->metricAssignment->setActiveAssignmentFieldValuesTo($this, $metricAssignmentReportDataProvider);
    }

    public function update(MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        if ($this->approved !== null) {
            $errorDetail = "forbidden: unable to update approved report";
            throw RegularException::forbidden($errorDetail);
        }
        
        $this->metricAssignment->setActiveAssignmentFieldValuesTo($this, $metricAssignmentReportDataProvider);

        $p = function (AssignmentFieldValue $assignmentFieldValue) {
            return $assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField();
        };
        foreach ($this->assignmentFieldValues->filter($p)->getIterator() as $assignmentFieldValue) {
            $assignmentFieldValue->remove();
        }
    }

    public function setAssignmentFieldValue(
            AssignmentField $assignmentField, AssignmentFieldValueData $assignmentFieldValueData): void
    {
        $p = function (AssignmentFieldValue $assignmentFieldValue) use ($assignmentField) {
            return $assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField($assignmentField);
        };
        $existingValue = $this->assignmentFieldValues->filter($p)->first();
        if (!empty($existingValue)) {
            $existingValue->update($assignmentFieldValueData);
        } else {
            $id = Uuid::generateUuid4();
            $assignmentFieldValue = new AssignmentFieldValue($this, $id, $assignmentField, $assignmentFieldValueData);
            $this->assignmentFieldValues->add($assignmentFieldValue);
        }
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->metricAssignment->belongsToTeam($team);
    }

}
