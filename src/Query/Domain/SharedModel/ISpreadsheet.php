<?php

namespace Query\Domain\SharedModel;

use Query\Domain\SharedModel\ReportSpreadsheet\ISheet;

interface ISpreadsheet
{
    public function createSheet(): ISheet;
    
    public function setLabel(string $label): void;
}
