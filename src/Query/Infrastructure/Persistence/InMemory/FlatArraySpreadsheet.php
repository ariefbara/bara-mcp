<?php

namespace Query\Infrastructure\Persistence\InMemory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet\ISheet;

class FlatArraySpreadsheet implements ISpreadsheet
{

    /**
     * 
     * @var FlatArraySheet[]
     */
    protected $sheets = [];

    public function __construct()
    {
        
    }

    public function createSheet(): ISheet
    {
        $sheet = new FlatArraySheet();
        $this->sheets[] = $sheet;
        return $sheet;
    }
    
    public function writeToXlsSpreadsheet(Spreadsheet $spreadsheet, ?bool $singleSheetMode = false)
    {
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

}
