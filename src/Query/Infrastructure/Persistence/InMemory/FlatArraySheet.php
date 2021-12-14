<?php

namespace Query\Infrastructure\Persistence\InMemory;

use Query\Domain\SharedModel\ReportSpreadsheet\ISheet;

class FlatArraySheet implements ISheet
{

    /**
     * 
     * @var string
     */
    protected $label;

    /**
     * 
     * @var array
     */
    protected $tableSheet = [];

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getTableSheet(): array
    {
        $sortedTable = [];
        foreach ($this->tableSheet as $entryRow) {
            ksort($entryRow);
            $sortedTable[] = $entryRow;
        }
        return $sortedTable;
    }

    public function __construct()
    {
    }

    public function insertIntoCell(int $rowNumber, int $colNumber, $value): void
    {
        $this->tableSheet[$rowNumber][$colNumber] = $value;
    }

    public function insertIntoSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): void
    {
        $sortedTable = [];
        foreach ($this->tableSheet as $entryRow) {
            ksort($entryRow);
            $sortedTable[] = $entryRow;
        }
        $validLabel = preg_replace("/[^A-Za-z0-9 _-]/", '', $this->label);
        $worksheet->setTitle(substr($validLabel, 0, 30));
        $worksheet->fromArray($sortedTable);
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

}
