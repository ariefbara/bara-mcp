<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{

    public function singleEvaluationPlanInProgram(string $programId, string $evaluationPlanId): EvaluationPlan;

    public function listEvaluationPlansInProgram(string $programId, int $page, int $pageSize, ?bool $disabledStatus);
}
