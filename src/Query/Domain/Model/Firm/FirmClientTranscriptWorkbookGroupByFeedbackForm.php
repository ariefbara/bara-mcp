<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\SharedModel\IWorkbook;
use Query\Domain\SharedModel\ReportSpreadsheet\ISheet;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheet;
use Query\Domain\SharedModel\ReportWorkbook;

class FirmClientTranscriptWorkbookGroupByFeedbackForm implements IReportSpreadsheet
{

    /**
     * 
     * @var ReportWorkbook
     */
    protected $reportWorkbook;

    /**
     * 
     * @var IWorkbook
     */
    protected $workbook;
    
    /**
     * 
     * @var callback[]
     */
    protected $feedbackFormSheetsBuilderCallback = [];

    public function __construct(IWorkbook $workbook)
    {
        $this->reportWorkbook = new ReportWorkbook();
        $this->workbook = $workbook;
    }
    
    public function inspectFeedbackForm(
            FeedbackForm $feedbackForm, ReportSpreadsheet\TeamMemberReportSheetPayload $teamMemberReportSheetPayload,
            ReportSpreadsheet\CustomFieldColumnsPayload $customFieldColumnsPayload): void
    {
        $this->feedbackFormSheetsBuilderCallback[] = 
        function (ISheet $sheet) use ($feedbackForm, $teamMemberReportSheetPayload, $customFieldColumnsPayload) {
            $teamMemberReportSheet = new TeamMemberReportSheet(
                    new InspectedClientList(), $sheet, $teamMemberReportSheetPayload);
            return $feedbackForm->buildFeedbackFormReportSheet($teamMemberReportSheet, $customFieldColumnsPayload);
        };
    }
    
    public function addClientTranscriptSpreadsheet(Client $client): void
    {
        $spreadsheet = $this->workbook->createSpreadsheet();
        $spreadsheet->setLabel($client->getFullName());
        $clientTranscriptSpreadsheet = $client->createTranscriptSpreadsheetGroupByFeedbackForm(
                $spreadsheet, $this->feedbackFormSheetsBuilderCallback);
        $this->reportWorkbook->addReportSpreadsheet($clientTranscriptSpreadsheet);
    }

    public function insertReport(EvaluationReport $report): void
    {
        $this->reportWorkbook->insertReport($report);
    }

}
