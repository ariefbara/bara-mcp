<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface EvaluationReportRepository
{

    public function anEvaluationReportBelongsToPersonnel(string $personnelId, string $id): EvaluationReport;

    public function allEvaluationReportsBelongsToPersonnel(
            string $personnelId, string $programId, int $page, int $pageSize,
            EvaluationReportFilter $evaluationReportFilter);

    public function allNonPaginatedActiveEvaluationReportsInProgram(
            Program $program, EvaluationReportSummaryFilter $evaluationReportSummaryFilter);

    public function allNonPaginatedActiveEvaluationReportsInFirm(
            Firm $firm, EvaluationReportSummaryFilter $evaluationReportSummaryFilter);

    public function allActiveEvaluationReportsBelongsToParticipant(string $participantId, int $page, int $pageSize);

    public function anActiveEvaluationReportBelongsToParticipant(string $participantId, string $id): EvaluationReport;
    
    public function allActiveEvaluationReportCorrespondWithClient(string $clientId, int $page, int $pageSize);
    
    public function anActiveEvaluationReportCorrespondWithClient(string $clientId, string $id): EvaluationReport;
}
