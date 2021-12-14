<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;


interface IReportColumn
{

    public function insertCorrespondingReportValue(EvaluationReport $report): void;
}
