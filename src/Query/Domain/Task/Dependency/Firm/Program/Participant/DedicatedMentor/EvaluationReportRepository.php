<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface EvaluationReportRepository
{

    public function anEvaluationReportBelongsToPersonnel(string $personnelId, string $id): EvaluationReport;

    public function allEvaluationReportsBelongsToPersonnel(
            string $personnelId, string $programId, int $page, int $pageSize, 
            EvaluationReportFilter $evaluationReportFilter);
    
    public function allNonPaginatedEvaluationReportsInProgram(
            Program $program, EvaluationReportSummaryFilter $evaluationReportSummaryFilter);
    
}
