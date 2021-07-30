<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

class EvaluationReportFilter
{

    /**
     * 
     * @var bool|null
     */
    protected $submittedStatus;

    /**
     * 
     * @var string|null
     */
    protected $evaluationPlanId;

    /**
     * 
     * @var string|null
     */
    protected $participantName;

    public function getSubmittedStatus(): ?bool
    {
        return $this->submittedStatus;
    }

    public function getEvaluationPlanId(): ?string
    {
        return $this->evaluationPlanId;
    }

    public function getParticipantName(): ?string
    {
        return $this->participantName;
    }

    public function __construct(?bool $submittedStatus, ?string $evaluationPlanId, ?string $participantName)
    {
        $this->submittedStatus = $submittedStatus;
        $this->evaluationPlanId = $evaluationPlanId;
        $this->participantName = $participantName;
    }

}
