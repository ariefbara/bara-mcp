<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\MetricAssignment;

class MetricAssignmentReportListFilterForConsultant
{

    /**
     * 
     * @var MetricAssignmentReportListFilter
     */
    protected $metricAssignmentReportListFilter;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    /**
     * 
     * @var bool
     */
    protected $onlyDedicatedMentee = false;

    public function setProgramId(?string $programId)
    {
        $this->programId = $programId;
        return $this;
    }

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

    public function setOnlyDedicatedMentee()
    {
        $this->onlyDedicatedMentee = true;
        return $this;
    }

    public function __construct(MetricAssignmentReportListFilter $metricAssignmentReportListFilter)
    {
        $this->metricAssignmentReportListFilter = $metricAssignmentReportListFilter;
    }

    protected function getProgramIdCriteria(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }

    protected function getParticipantIdCriteria(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND MetricAssignment.Participant_id = :participantId
_STATEMENT;
    }
    
    protected function getOnlyDedicatedMenteeCriteria()
    {
        return !$this->onlyDedicatedMentee ? null : <<<_STATEMENT
    AND DedicatedMentor.id IS NOT NULL
_STATEMENT;
    }

    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->metricAssignmentReportListFilter->getCriteriaStatement($parameters)
                . $this->getProgramIdCriteria($parameters)
                . $this->getParticipantIdCriteria($parameters);
    }

    public function getOrderStatement(): string
    {
        return $this->metricAssignmentReportListFilter->getOrderStatement();
    }

    public function getLimitStatement(): string
    {
        return $this->metricAssignmentReportListFilter->getLimitStatement();
    }

}
