<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm\Client\ClientSingleTableTrascriptSheet;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;

class FirmClientSingleTableTranscriptSpreadsheet implements IReportSpreadsheet
{

    /**
     * 
     * @var ISpreadsheet
     */
    protected $spreadsheet;
    
    /**
     * 
     * @var ClientSingleTableTrascriptSheet[]
     */
    protected $clientSingleTableTrascriptSheets = [];

    /**
     * 
     * @var TeamMemberReportSheetPayload
     */
    protected $payload;

    public function __construct(ISpreadsheet $spreadsheet, TeamMemberReportSheetPayload $payload)
    {
        $this->reportSpreadsheet = new ReportSpreadsheet();
        $this->spreadsheet = $spreadsheet;
        $this->payload = $payload;
    }
    
    public function inspectClient(Client $client): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $inspectedClientList = new InspectedClientList();
        $teamMemberReportSheet = new TeamMemberReportSheet($inspectedClientList, $sheet, $this->payload);
        $this->clientSingleTableTrascriptSheets[] = 
                new ClientSingleTableTrascriptSheet($client, $teamMemberReportSheet);
    }

    public function insertReport(EvaluationReport $report): void
    {
        foreach ($this->clientSingleTableTrascriptSheets as $sheet) {
            $sheet->includeReport($report);
        }
    }

}
