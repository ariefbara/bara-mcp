<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\{
    Coordinator,
    EvaluationPlan,
    Participant
};
use SharedContext\Domain\ValueObject\EvaluationResult;

class Evaluation
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
     * @var EvaluationPlan
     */
    protected $evaluationPlan;

    /**
     *
     * @var Coordinator
     */
    protected $coordinator;

    /**
     *
     * @var EvaluationResult
     */
    protected $evaluationResult;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getEvaluationPlan(): EvaluationPlan
    {
        return $this->evaluationPlan;
    }

    function getCoordinator(): Coordinator
    {
        return $this->coordinator;
    }

    function getSubmitTime(): DateTimeImmutable
    {
        return $this->submitTime;
    }

    protected function __construct()
    {
        
    }

    public function getStatus(): string
    {
        return $this->evaluationResult->getStatus();
    }

    public function getExtendDays(): ?int
    {
        return $this->evaluationResult->getExtendDays();
    }

    function getSubmitTimeString(): string
    {
        return $this->submitTime->format("Y-m-d H:i:s");
    }

}
