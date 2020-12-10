<?php

namespace User\Application\Service\Personnel\Coordinator;

use User\Domain\DependencyModel\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{

    public function ofId(string $evaluationPlanId): EvaluationPlan;
}
