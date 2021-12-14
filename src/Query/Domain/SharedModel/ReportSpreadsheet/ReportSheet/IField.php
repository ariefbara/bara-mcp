<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;

interface IField
{

    public function getLabel(): string;

    public function getCorrespondingValueFromEvaluationReport(EvaluationReport $report);
}
