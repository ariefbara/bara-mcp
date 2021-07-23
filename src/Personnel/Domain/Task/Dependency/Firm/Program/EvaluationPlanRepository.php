<?php

namespace Personnel\Domain\Task\Dependency\Firm\Program;

use Personnel\Domain\Model\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{
    public function ofId(string $id): EvaluationPlan;
}
