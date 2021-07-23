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
    protected $participantId;

    public function getSubmittedStatus(): ?bool
    {
        return $this->submittedStatus;
    }

    public function getEvaluationPlanId(): ?string
    {
        return $this->evaluationPlanId;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function __construct(?bool $submittedStatus, ?string $evaluationPlanId, ?string $participantId)
    {
        $this->submittedStatus = $submittedStatus;
        $this->evaluationPlanId = $evaluationPlanId;
        $this->participantId = $participantId;
    }

}
