<?php

namespace Query\Domain\Model\Firm\Client;

use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet;
use User\Domain\DependencyModel\Firm\FeedbackForm;

class ClientTranscriptSpreadsheetGroupByFeedbackForm implements IReportSpreadsheet
{
    /**
     * 
     * @var Client
     */
    protected $client;
    
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
    
    public function __construct(Client $client, ISpreadsheet $spreadsheet, array $feedbackFormSheetsBuilderCallback)
    {
        $this->client = $client;
        $this->reportSpreadsheet = new ReportSpreadsheet();
        $this->spreadsheet = $spreadsheet;
        foreach ($feedbackFormSheetsBuilderCallback as $feedbackFormSheetBuilderCallback) {
            $sheet = $this->spreadsheet->createSheet();
            $this->reportSpreadsheet->addReportSheet($feedbackFormSheetBuilderCallback($sheet));
        }
    }

    public function insertReport(EvaluationReport $report): void
    {
        if (!$report->correspondWithClient($this->client)) {
            return;
        }
        if (!$this->reportSpreadsheet->insertReport($report)) {
            $inspectedClientList = new InspectedClientList();
            $sheet = $this->spreadsheet->createSheet();
            $reportSheetPayload = (new ReportSpreadsheet\ReportSheetPayload())
                    ->inspectEvaluation(2)
                    ->inspectEvaluator(3)
                    ->inspectSubmitTime(4);
            $payload = (new ReportSpreadsheet\TeamMemberReportSheetPayload($reportSheetPayload))
                    ->inspectTeam(1);
            $teamMemberReportSheet = new ReportSpreadsheet\TeamMemberReportSheet($inspectedClientList, $sheet, $payload);
            $feedbackFormSheet = $report->buildFeedbackFormReportSheetAndIncludeReport($teamMemberReportSheet);
            $this->reportSpreadsheet->addReportSheet($feedbackFormSheet);
        }
    }

}
