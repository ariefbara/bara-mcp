<?php

namespace Query\Domain\SharedModel\ReportSpreadsheet;

interface ISheet
{

    public function insertIntoCell(int $rowNumber, int $colNumber, $value): void;

    public function setLabel(string $label): void;
}
