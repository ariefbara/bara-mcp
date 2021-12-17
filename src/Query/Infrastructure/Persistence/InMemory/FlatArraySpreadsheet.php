<?php

namespace Query\Infrastructure\Persistence\InMemory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet\ISheet;

class FlatArraySpreadsheet implements ISpreadsheet
{

    /**
     * 
     * @var string|null
     */
    protected $label;

    /**
     * 
     * @var FlatArraySheet[]
     */
    protected $sheets = [];

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function __construct()
    {
        
    }

    public function createSheet(): ISheet
    {
        $sheet = new FlatArraySheet();
        $this->sheets[] = $sheet;
        return $sheet;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getTableSheet(): array
    {
        $sortedTable = [];
        foreach ($this->sheets as $sheet) {
            $sortedTable = array_merge($sortedTable, [['']], [[$sheet->getLabel()]], $sheet->getTableSheet());
        }
        return $sortedTable;
    }

    public function writeToXlsSpreadsheet(Spreadsheet $spreadsheet, ?bool $singleSheetMode = false)
    {
        if (empty($this->sheets)) {
            $spreadsheet->createSheet();
        }
        if ($singleSheetMode) {
            $worksheet = $spreadsheet->createSheet();
            $reportTable = [];
            foreach ($this->sheets as $sheet) {
                $reportTable = array_merge($reportTable, [['']], [[$sheet->getLabel()]], $sheet->getTableSheet());
            }
            $worksheet->fromArray($reportTable);
        } else {
            foreach ($this->sheets as $sheet) {
                $worksheet = $spreadsheet->createSheet();
                $sheet->insertIntoSheet($worksheet);
            }
        }
    }

    public function writeToXlsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet): void
    {
        $validLabel = preg_replace("/[^A-Za-z0-9 _-]/", '', $this->label);
        $worksheet->setTitle(substr($validLabel, 0, 30));

        $reportTable = [];
        foreach ($this->sheets as $sheet) {
            $reportTable = array_merge($reportTable, [['']], [[$sheet->getLabel()]], $sheet->getTableSheet());
        }
        $worksheet->fromArray($reportTable);
    }

}
