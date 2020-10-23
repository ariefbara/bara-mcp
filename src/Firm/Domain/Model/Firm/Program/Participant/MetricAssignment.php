<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\ {
    Model\Firm\Program\Metric,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\MetricAssignment\AssignmentField,
    Service\MetricAssignmentDataProvider
};
use Resources\ {
    Domain\ValueObject\DateInterval,
    Exception\RegularException,
    Uuid,
    ValidationRule,
    ValidationService
};

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

    public function setStartEndDate(?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate)
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($startDate, "bad request: metric assignment start date is mandatory");
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($endDate, "bad request: metric assignment end date is mandatory");
        
        $this->startEndDate = new DateInterval($startDate, $endDate);
    }
    
    public function __construct(
            Participant $participant, string $id, MetricAssignmentDataProvider $metricAssignmentDataProvider)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->setStartEndDate($metricAssignmentDataProvider->getStartDate(), $metricAssignmentDataProvider->getEndDate());
        
        $this->assignmentFields = new ArrayCollection();
        $this->addAssignmentFields($metricAssignmentDataProvider);
    }

    public function update(MetricAssignmentDataProvider $metricAssignmentDataProvider): void
    {
        $this->setStartEndDate($metricAssignmentDataProvider->getStartDate(), $metricAssignmentDataProvider->getEndDate());
        foreach ($this->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentField->update($metricAssignmentDataProvider);
        }
        $this->addAssignmentFields($metricAssignmentDataProvider);
    }
    
    protected function addAssignmentFields(MetricAssignmentDataProvider $metricAssignmentDataProvider): void
    {
        foreach ($metricAssignmentDataProvider->iterateMetrics() as $metric) {
            $this->assertMetricInSameProgramAsParticipant($metric);
            $id = Uuid::generateUuid4();
            $target = $metricAssignmentDataProvider->pullTargetCorrespondWithMetric($metric);
            $assignmentField = new AssignmentField($this, $id, $metric, $target);
            $this->assignmentFields->add($assignmentField);
        }
    }
    protected function assertMetricInSameProgramAsParticipant(Metric $metric): void
    {
        if (!$this->participant->belongsInTheSameProgramAs($metric)) {
            $errorDetail = "forbidden : unable to assign metric from other program";
            throw RegularException::forbidden($errorDetail);
        }
    }
    /**
     * 
     * @return AssignmentField[]
     */
    protected function iterateActiveAssignmentFields()
    {
        $criteria = \Doctrine\Common\Collections\Criteria::create()
                ->andWhere(\Doctrine\Common\Collections\Criteria::expr()->eq("removed", false));
        return $this->assignmentFields->matching($criteria)->getIterator();
    }

}
