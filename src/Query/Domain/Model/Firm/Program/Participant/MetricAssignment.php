<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\Firm\Program\ {
    Participant,
    Participant\MetricAssignment\AssignmentField
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
    public function iterateNonRemovedAssignmentFields()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("removed", false));
        return $this->assignmentFields->matching($criteria)->getIterator();
    }

}
