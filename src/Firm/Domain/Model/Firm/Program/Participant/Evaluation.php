<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program\ {
    Coordinator,
    EvaluationPlan,
    Participant
};
use Resources\DateTimeImmutableBuilder;
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

    function __construct(
            Participant $participant, string $id, EvaluationPlan $evaluationPlan, EvaluationData $evaluationData,
            Coordinator $coordinator)
    {
        $this->participant = $participant;
        $this->id = $id;
        $this->evaluationPlan = $evaluationPlan;
        $this->coordinator = $coordinator;
        $this->evaluationResult = new EvaluationResult($evaluationData->getStatus(), $evaluationData->getExtendDays());
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        
        if ($this->evaluationResult->isFail()) {
            $this->participant->disable();
        }
    }

}
