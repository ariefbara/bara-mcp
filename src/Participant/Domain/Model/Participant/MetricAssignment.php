<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Participant\Domain\{
    DependencyModel\Firm\Client\AssetBelongsToTeamInterface,
    DependencyModel\Firm\Team,
    Model\Participant,
    Model\Participant\MetricAssignment\AssignmentField,
    Model\Participant\MetricAssignment\MetricAssignmentReport,
    Service\MetricAssignmentReportDataProvider
};
use Resources\{
    Domain\ValueObject\DateInterval,
    Exception\RegularException
};

class MetricAssignment implements AssetBelongsToTeamInterface
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var DateInterval
     */
    protected $startEndDate;

    /**
     *
     * @var ArrayCollection
     */
    protected $assignmentFields;

    protected function __construct()
    {
        
    }

    public function belongsToTeam(Team $team): bool
    {
        return $this->participant->belongsToTeam($team);
    }

    public function submitReport(
            string $metricAssignmentReportId, DateTimeImmutable $observationTime,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): MetricAssignmentReport
    {
        if (!$this->startEndDate->contain($observationTime)) {
            $errorDetail = "forbidden: observation time out of bound";
            throw RegularException::forbidden($errorDetail);
        }

        return new MetricAssignmentReport(
                $this, $metricAssignmentReportId, $observationTime, $metricAssignmentReportDataProvider);
    }

    public function setActiveAssignmentFieldValuesTo(
            MetricAssignmentReport $metricAssignmentReport,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        if (!$this->participant->ownAllAttachedFileInfo($metricAssignmentReportDataProvider)) {
            $errorDetail = "forbidden: can only attached owned file";
            throw RegularException::forbidden($errorDetail);
        }
        foreach ($this->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentField->setValueIn($metricAssignmentReport, $metricAssignmentReportDataProvider);
        }
    }

    /**
     * 
     * @return AssignmentField[]
     */
    protected function iterateActiveAssignmentFields()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("disabled", false));
        return $this->assignmentFields->matching($criteria)->getIterator();
    }

}
