<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface IReportSpreadsheet
{

    public function insertReport(EvaluationReport $report): void;
}
