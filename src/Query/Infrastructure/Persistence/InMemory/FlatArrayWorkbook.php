<?php

namespace Query\Infrastructure\Persistence\InMemory;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\IWorkbook;

class FlatArrayWorkbook implements IWorkbook
{

    /**
     * 
     * @var FlatArraySpreadsheet[]
     */
    protected $spreadsheets = [];

    public function __construct()
    {
        
    }

    public function createSpreadsheet(): ISpreadsheet
    {
        $spreadsheet = new FlatArraySpreadsheet();
        $this->spreadsheets[] = $spreadsheet;
        return $spreadsheet;
    }

    public function writeToXlsSpreadsheet(Spreadsheet $spreadsheet, ?bool $singleSheetMode = false)
    {
        if (empty($this->spreadsheets)) {
            $spreadsheet->createSheet();
        }
        if ($singleSheetMode) {
            $worksheet = $spreadsheet->createSheet();
            $reportTable = [];
            foreach ($this->spreadsheets as $reportSpreadsheet) {
                $reportTable = array_merge($reportTable, [['']], [[$reportSpreadsheet->getLabel()]],
                        $reportSpreadsheet->getTableSheet());
            }
            $worksheet->fromArray($reportTable);
        } else {
            foreach ($this->spreadsheets as $reportSpreadsheet) {
                $worksheet = $spreadsheet->createSheet();
                $reportSpreadsheet->writeToXlsSheet($worksheet);
            }
        }
    }

}
