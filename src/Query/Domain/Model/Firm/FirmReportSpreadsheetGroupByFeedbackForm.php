<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Team\Member\InspectedClientList;
use Query\Domain\SharedModel\IReportSpreadsheet;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet;

class FirmReportSpreadsheetGroupByFeedbackForm implements IReportSpreadsheet
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
            InspectedClientList $inspectedClientList, ISpreadsheet $spreadsheet)
    {
        $this->inspectedClientList = $inspectedClientList;
        $this->reportSpreadsheet = new ReportSpreadsheet();
        $this->spreadsheet = $spreadsheet;
    }

    public function addReportSheet(
            FeedbackForm $feedbackForm, ReportSpreadsheet\TeamMemberReportSheetPayload $teamMemberReportSheetPayload,
            ReportSpreadsheet\CustomFieldColumnsPayload $customFieldColumnsPayload): void
    {
        $sheet = $this->spreadsheet->createSheet();
        $teamMemberReportSheet = new ReportSpreadsheet\TeamMemberReportSheet($this->inspectedClientList, $sheet,
                $teamMemberReportSheetPayload);
        $feedbackFormReportSheet = $feedbackForm->buildFeedbackFormReportSheet(
                $teamMemberReportSheet, $customFieldColumnsPayload);
        $this->reportSpreadsheet->addReportSheet($feedbackFormReportSheet);
    }

    public function insertReport(EvaluationReport $report): void
    {
        if (!$this->reportSpreadsheet->insertReport($report)) {
            $inspectedClientList = $this->inspectedClientList;
            $sheet = $this->spreadsheet->createSheet();
            $reportSheetPayload = (new ReportSpreadsheet\ReportSheetPayload())
                    ->inspectEvaluation(3)
                    ->inspectEvaluator(4)
                    ->inspectSubmitTime(5);
            $payload = (new ReportSpreadsheet\TeamMemberReportSheetPayload($reportSheetPayload))
                    ->inspectIndividual(1)
                    ->inspectTeam(2);
            $teamMemberReportSheet = new ReportSpreadsheet\TeamMemberReportSheet($inspectedClientList, $sheet, $payload);
            $feedbackFormSheet = $report->buildFeedbackFormReportSheetAndIncludeReport($teamMemberReportSheet);
            $this->reportSpreadsheet->addReportSheet($feedbackFormSheet);
        }
    }

}
