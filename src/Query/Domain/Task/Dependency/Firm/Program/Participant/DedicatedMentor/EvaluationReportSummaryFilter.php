<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

class EvaluationReportSummaryFilter
{

    /**
     * 
     * @var array
     */
    protected $evaluationPlanIdList;

    /**
     * 
     * @var array
     */
    protected $participantIdList;

    public function getEvaluationPlanIdList(): array
    {
        return $this->evaluationPlanIdList;
    }

    public function getParticipantIdList(): array
    {
        return $this->participantIdList;
    }

    public function __construct()
    {
        $this->evaluationPlanIdList = [];
        $this->participantIdList = [];
    }

    public function addEvaluationPlanId(string $evaluationPlanId): self
    {
        $this->evaluationPlanIdList[] = $evaluationPlanId;
        return $this;
    }

    public function addParticipantId(string $participantId): self
    {
        $this->participantIdList[] = $participantId;
        return $this;
    }

}
