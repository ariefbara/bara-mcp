<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;

interface EvaluationReportRepository
{

    public function anEvaluationReportBelongsToPersonnel(
            string $firmId, string $personnelId, string $participantId, string $evaluationPlanId): EvaluationReport;

    public function allEvaluationReportsBelongsToCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize);
}
