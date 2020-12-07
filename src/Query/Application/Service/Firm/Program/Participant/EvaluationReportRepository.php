<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;

interface EvaluationReportRepository
{

    public function anEvaluationReportInProgram(string $firmId, string $programId, string $evaluationReportId): EvaluationReport;

    public function allEvaluationReportsBelongsToProgramParticipant(
            string $firmId, string $programId, $participantId, int $page, int $pageSize, ?string $evaluationPlanId);
}
