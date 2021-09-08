<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface IProgramEvaluationReportSummaryResult
{

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void;
}
