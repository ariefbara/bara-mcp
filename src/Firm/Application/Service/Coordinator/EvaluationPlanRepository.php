<?php

namespace Firm\Application\Service\Coordinator;

use Firm\Domain\Model\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{
    public function ofId(string $evaluationPlanId): EvaluationPlan;
}
