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
            $errorDetail = "forbidden: observe time out of bound";
            throw RegularException::forbidden($errorDetail);
        }

        $metricAssignmentReport = new MetricAssignmentReport($this, $metricAssignmentReportId, $observationTime);
        $this->setActiveAssignmentFieldValuesTo($metricAssignmentReport, $metricAssignmentReportDataProvider);
        return $metricAssignmentReport;
    }

    public function setActiveAssignmentFieldValuesTo(
            MetricAssignmentReport $metricAssignmentReport,
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): void
    {
        foreach ($this->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentField->setValueIn($metricAssignmentReport, $metricAssignmentReportDataProvider);
        }
    }

    public function isParticipantOwnAllAttachedFileInfo(
            MetricAssignmentReportDataProvider $metricAssignmentReportDataProvider): bool
    {
        return $this->participant->ownAllAttachedFileInfo($metricAssignmentReportDataProvider);
    }

    /**
     * 
     * @return AssignmentField[]
     */
    protected function iterateActiveAssignmentFields()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("removed", false));
        return $this->assignmentFields->matching($criteria)->getIterator();
    }

}
