<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{

    public function anEvaluationPlanInProgram(string $firmid, string $programId, string $evaluationPlanId): EvaluationPlan;

    public function anEvaluationPlanInFirm(string $firmid, string $evaluationPlanId): EvaluationPlan;

    public function allEvaluationPlansInProgram(
            string $firmid, string $programId, int $page, int $pageSize, ?bool $enableOnly = true);
}
