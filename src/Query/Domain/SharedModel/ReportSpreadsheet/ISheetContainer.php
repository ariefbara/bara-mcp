<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheet\IField;

interface ISheetContainer extends IReportSheet
{

    public function addFieldColumn(IField $field, ?int $colNumber): void;

    public function setLabel(string $label): void;
}
