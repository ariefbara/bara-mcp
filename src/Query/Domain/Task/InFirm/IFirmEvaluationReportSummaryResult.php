<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface IFirmEvaluationReportSummaryResult
{

    public function includeEvaluationReport(EvaluationReport $evaluationReport): void;
}
