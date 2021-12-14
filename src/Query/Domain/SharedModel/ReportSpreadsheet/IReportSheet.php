<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface IReportSheet
{
    public function includeReport(EvaluationReport $report): bool;
}
