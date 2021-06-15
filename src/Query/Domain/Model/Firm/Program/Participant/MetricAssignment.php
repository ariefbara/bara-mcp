<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Firm\Program\ {
    Participant,
    Participant\MetricAssignment\AssignmentField,
    Participant\MetricAssignment\MetricAssignmentReport
};
use Resources\Domain\ValueObject\DateInterval;

class MetricAssignment
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
    
    /**
     *
     * @var ArrayCollection
     */
    protected $metricAssignmentReports;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getStartDateString(): string
    {
        return $this->startEndDate->getStartDateString();
    }

    public function getEndDateString(): string
    {
        return $this->startEndDate->getEndDateString();
    }

    /**
     * 
     * @return AssignmentField[]
     */
    public function iterateActiveAssignmentFields()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("disabled", false));
        return $this->assignmentFields->matching($criteria)->getIterator();
    }
    
    public function getLastApprovedMetricAssignmentReports(): ?MetricAssignmentReport
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("approved", true))
                ->andWhere(Criteria::expr()->eq("removed", false))
                ->orderBy(["observationTime" => "DESC"])
                ->setMaxResults(1);
        $metricAssignmentReport = $this->metricAssignmentReports->matching($criteria)->first();
        return empty($metricAssignmentReport)? null: $metricAssignmentReport;
    }

}
