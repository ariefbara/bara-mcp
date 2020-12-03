<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;

interface EvaluationReportRepository
{

    public function anEvaluationReportInProgram(string $firmId, string $programId, string $evaluationReportId): EvaluationReport;

    public function allEvaluationReportsInProgram(string $firmId, string $programId, int $page, int $pageSize);
}
