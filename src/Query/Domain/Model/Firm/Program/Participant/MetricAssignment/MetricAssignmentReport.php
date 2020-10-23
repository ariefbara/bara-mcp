<?php

namespace Query\Domain\Model\Firm\Program\Participant\MetricAssignment;

use DateTimeImmutable;
use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\ {
    Firm\Program\Participant\MetricAssignment,
    Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};

class MetricAssignmentReport
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
    
    public function getMetricAssignment(): MetricAssignment
    {
        return $this->metricAssignment;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getObserveTimeString(): string
    {
        return $this->observeTime->format("Y-m-d H:i:s");
    }

    public function getSubmitTimeString(): string
    {
        return $this->submitTime->format("Y-m-d H:i:s");
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    
    protected function __construct()
    {
        
    }
    
    /**
     * 
     * @return AssignmentFieldValue[]
     */
    public function iterateNonremovedAssignmentFieldValues()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("removed", false));
        return $this->assignmentFieldValues->matching($criteria)->getIterator();
    }

}
