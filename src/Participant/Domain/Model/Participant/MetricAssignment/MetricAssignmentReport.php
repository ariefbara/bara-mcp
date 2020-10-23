<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\{
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Team,
    Model\Participant\MetricAssignment,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};
use Resources\{
    DateTimeImmutableBuilder,
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
    protected $observeTime;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

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

    public function __construct(MetricAssignment $metricAssignment, string $id, DateTimeImmutable $observeTime)
    {
        $this->metricAssignment = $metricAssignment;
        $this->id = $id;
        $this->observeTime = $observeTime;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;
        $this->assignmentFieldValues = new ArrayCollection();
    }

    public function update(MetricAssignmentReportData $metricAssignmentReportData): void
    {
        $this->metricAssignment->setActiveAssignmentFieldValuesTo($this, $metricAssignmentReportData);

        $p = function (AssignmentFieldValue $assignmentFieldValue) {
            return $assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField();
        };
        foreach ($this->assignmentFieldValues->filter($p)->getIterator() as $assignmentFieldValue) {
            $assignmentFieldValue->remove();
        }
    }

    public function setAssignmentFieldValue(AssignmentField $assignmentField, ?float $value): void
    {
        $p = function (AssignmentFieldValue $assignmentFieldValue) use ($assignmentField) {
            return $assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField($assignmentField);
        };
        $existingValue = $this->assignmentFieldValues->filter($p)->first();
        if (!empty($existingValue)) {
            $existingValue->update($value);
        } else {
            $id = Uuid::generateUuid4();
            $assignmentFieldValue = new AssignmentFieldValue($this, $id, $assignmentField, $value);
            $this->assignmentFieldValues->add($assignmentFieldValue);
        }
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->metricAssignment->belongsToTeam($team);
    }

}
