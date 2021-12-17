<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;

class FirmSingleTableReportSpreadsheet implements IReportSpreadsheet
{

    /**
     * 
     * @var InspectedClientList
     */
    protected $inspectedClientList;

    /**
     * 
     * @var ReportSpreadsheet
     */
    protected $reportSpreadsheet;

    /**
     * 
     * @var ISpreadsheet
     */
    protected $spreadsheet;

    public function __construct(
            InspectedClientList $inspectedClientList, ISpreadsheet $spreadsheet,
            TeamMemberReportSheetPayload $payload)
    {
        $this->inspectedClientList = $inspectedClientList;
        $this->reportSpreadsheet = new ReportSpreadsheet();
        $this->spreadsheet = $spreadsheet;
        
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setLabel('evaluation report summary');
        $teamMemberReportSheet = new TeamMemberReportSheet($this->inspectedClientList, $sheet, $payload);
        $this->reportSpreadsheet->addReportSheet($teamMemberReportSheet);
    }

    public function insertReport(EvaluationReport $report): void
    {
        $this->reportSpreadsheet->insertReport($report);
    }

}
