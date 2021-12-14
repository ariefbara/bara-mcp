<?php

namespace App\Http\Controllers\Manager;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\FirmReportSpreadsheetGroupByFeedbackForm;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\SharedModel\ISpreadsheet;
use Query\Domain\SharedModel\ReportSpreadsheet\CustomFieldColumnsPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\ReportSheetPayload;
use Query\Domain\SharedModel\ReportSpreadsheet\TeamMemberReportSheetPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\InFirm\BuildFirmClientTranscriptWorkbookGroupByFeedbackFormTask;
use Query\Domain\Task\InFirm\BuildReportGroupByFeedbackFormPayload;
use Query\Domain\Task\InFirm\BuildReportSpreadsheetGroupByFeedbackFormTask;
use Query\Domain\Task\InFirm\ClientEvaluationReportSummaryResult;
use Query\Domain\Task\InFirm\ClientEvaluationReportTranscriptResult;
use Query\Domain\Task\InFirm\FeedbackFormReportSheetRequest;
use Query\Domain\Task\InFirm\GenerateFirmEvaluationReportSummaryTask;
use Query\Domain\Task\InFirm\GenerateFirmEvaluationReportTranscriptTask;
use Query\Domain\Task\InFirm\WriteEvaluationReportToSpreadsheetTask;
use Query\Infrastructure\Persistence\InMemory\FlatArraySpreadsheet;
use Query\Infrastructure\Persistence\InMemory\FlatArrayWorkbook;

class EvaluationReportSummaryController extends ManagerBaseController
{

    public function summary()
    {
        $result = new ClientEvaluationReportSummaryResult();
        $this->generateFirmEvaluationReportSummary($result);
        return $this->singleQueryResponse($result->toRelationalArray());
    }

    public function downloadSummaryXls()
    {
        $result = new ClientEvaluationReportSummaryResult();
        $this->generateFirmEvaluationReportSummary($result);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $result->saveToSpreadsheet($spreadsheet);

        $writer = new Xlsx($spreadsheet);

        return $this->sendXlsDownloadResponse($writer);
    }

    public function transcript()
    {
        $result = new ClientEvaluationReportTranscriptResult();
        $this->generateFirmEvaluationReportTranscript($result);
        return $this->singleQueryResponse($result->toRelationalArray());
    }

    public function downloadTranscriptXls()
    {
        $summaryStyleView = $this->filterBooleanOfQueryRequest('summaryStyleView') ? true : false;

        $result = new ClientEvaluationReportTranscriptResult();
        $this->generateFirmEvaluationReportTranscript($result);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $result->saveToSpreadsheet($spreadsheet, $summaryStyleView);

        $writer = new Xlsx($spreadsheet);

        return $this->sendXlsDownloadResponse($writer, 'evaluation-report-transcript.xls');
    }
    
    public function downloadSummaryXlsGroupByFeedbackForm()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $flatArraySpreadsheet = new FlatArraySpreadsheet();
        $reportSpreadsheet = $this->buildAndExecuteReportSpreadsheetGroupByFeedbackFormTask($flatArraySpreadsheet);
        $task = new WriteEvaluationReportToSpreadsheetTask(
                $evaluationReportRepository, $reportSpreadsheet, $this->getEvaluationReportSummaryFilter());
        $this->executeFirmQueryTask($task);
        
        $singleSheetMode = $this->filterBooleanOfQueryRequest('singleSheetMode');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $flatArraySpreadsheet->writeToXlsSpreadsheet($spreadsheet, $singleSheetMode);
        
        $writer = new Xlsx($spreadsheet);

        return $this->sendXlsDownloadResponse($writer, 'evaluation-report-summary.xls');
    }
    
    public function downloadTranscriptXlsGroupByFeedbackForm()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $workbook = new FlatArrayWorkbook();
        $reportSpreadsheet = $this->buildAndExecuteFirmClientTranscriptWorkbookGroupByFeedbackForm($workbook);
        $task = new WriteEvaluationReportToSpreadsheetTask(
                $evaluationReportRepository, $reportSpreadsheet, $this->getEvaluationReportSummaryFilter());
        $this->executeFirmQueryTask($task);
        
        $singleSheetMode = $this->filterBooleanOfQueryRequest('singleSheetMode');
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $workbook->writeToXlsSpreadsheet($spreadsheet, $singleSheetMode);
        
        $writer = new Xlsx($spreadsheet);

        return $this->sendXlsDownloadResponse($writer, 'client-transcripts.xls');
    }
    
    protected function buildAndExecuteFirmClientTranscriptWorkbookGroupByFeedbackForm($workbook)
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);
        $payload = $this->getBuildReportGroupByFeedbackFormPayload();
        $task = new BuildFirmClientTranscriptWorkbookGroupByFeedbackFormTask($clientRepository, $feedbackFormRepository, $workbook, $payload);
        $this->executeFirmQueryTask($task);
        return $task->result;
    }
    
    protected function buildAndExecuteReportSpreadsheetGroupByFeedbackFormTask(ISpreadsheet $spreadsheet): FirmReportSpreadsheetGroupByFeedbackForm
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $payload = $this->getBuildReportGroupByFeedbackFormPayload();
        $task = new BuildReportSpreadsheetGroupByFeedbackFormTask(
                $feedbackFormRepository, $clientRepository, $spreadsheet, $payload);
        $this->executeFirmQueryTask($task);
        return $task->result;
    }
    
    protected function getBuildReportGroupByFeedbackFormPayload(): BuildReportGroupByFeedbackFormPayload
    {
        $payload = new BuildReportGroupByFeedbackFormPayload();
        
        $clientIdList = $this->request->query('clientIdList');
        if (!empty($clientIdList)) {
            foreach ($clientIdList as $clientId) {
                $payload->inspectClient($this->stripTagsVariable($clientId));
            }
        }
        
        $inspectedFeedbackFormList = $this->request->query('inspectedFeedbackFormList');
        if (!empty($inspectedFeedbackFormList)) {
            foreach ($inspectedFeedbackFormList as $inspectedFeedbackForm) {
                $feedbackFormId = $this->stripTagsVariable($inspectedFeedbackForm['id']);

                $reportSheetPayload = new ReportSheetPayload();
                if (isset($inspectedFeedbackForm['evaluationColNumber'])) {
                    $reportSheetPayload->inspectEvaluation($this->integerOfVariable($inspectedFeedbackForm['evaluationColNumber']));
                }
                if (isset($inspectedFeedbackForm['mentorColNumber'])) {
                    $reportSheetPayload->inspectEvaluator($this->integerOfVariable($inspectedFeedbackForm['mentorColNumber']));
                }
                if (isset($inspectedFeedbackForm['submitTimeColNumber'])) {
                    $reportSheetPayload->inspectSubmitTime($this->integerOfVariable($inspectedFeedbackForm['submitTimeColNumber']));
                }

                $teamMemberReportSheetPayload = new TeamMemberReportSheetPayload($reportSheetPayload);
                if (isset($inspectedFeedbackForm['teamColNumber'])) {
                    $teamMemberReportSheetPayload->inspectTeam($this->integerOfVariable($inspectedFeedbackForm['teamColNumber']));
                }
                if (isset($inspectedFeedbackForm['individualColNumber'])) {
                    $teamMemberReportSheetPayload->inspectIndividual($this->integerOfVariable($inspectedFeedbackForm['individualColNumber']));
                }

                $customFieldColumnsPayload = new CustomFieldColumnsPayload();
                $customFieldColumnList = $this->request->query('customFieldColumnList');
                if (!empty($customFieldColumnList)) {
                    foreach ($customFieldColumnList as $customFieldColumn) {
                        $customFieldColumnsPayload->inspectField($customFieldColumn['fieldId'], $customFieldColumn['colNumber']);
                    }
                }
                $feedbackFormReportSheetRequest = new FeedbackFormReportSheetRequest($feedbackFormId, $teamMemberReportSheetPayload, $customFieldColumnsPayload);
                $payload->reportFeedbackForm($feedbackFormReportSheetRequest);
            }
        }
        return $payload;
    }

    protected function generateFirmEvaluationReportSummary(ClientEvaluationReportSummaryResult $result): void
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new GenerateFirmEvaluationReportSummaryTask(
                $evaluationReportRepository, $this->getEvaluationReportSummaryFilter(), $result);
        $this->executeFirmQueryTask($task);
    }

    protected function generateFirmEvaluationReportTranscript(ClientEvaluationReportTranscriptResult $result): void
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new GenerateFirmEvaluationReportTranscriptTask(
                $clientRepository, $evaluationReportRepository, $this->getEvaluationReportSummaryFilter(), $result);
        $this->executeFirmQueryTask($task);
    }

    protected function getEvaluationReportSummaryFilter(): EvaluationReportSummaryFilter
    {
        $evaluationReportSummaryFilter = new EvaluationReportSummaryFilter();
        $evaluationPlanIdList = $this->request->query('evaluationPlanIdList');
        $clientIdList = $this->request->query('clientIdList');
        $personnelIdList = $this->request->query('personnelIdList');
        $inspectedFeedbackFormList = $this->request->query('inspectedFeedbackFormList');

        if (!empty($evaluationPlanIdList)) {
            foreach ($evaluationPlanIdList as $evaluationPlanId) {
                $evaluationReportSummaryFilter->addEvaluationPlanId($this->stripTagsVariable($evaluationPlanId));
            }
        }
        if (!empty($clientIdList)) {
            foreach ($clientIdList as $clientId) {
                $evaluationReportSummaryFilter->addClientId($this->stripTagsVariable($clientId));
            }
        }
        if (!empty($personnelIdList)) {
            foreach ($personnelIdList as $personnelId) {
                $evaluationReportSummaryFilter->addPersonnelId($this->stripTagsVariable($personnelId));
            }
        }
        if (!empty($inspectedFeedbackFormList)) {
            foreach ($inspectedFeedbackFormList as $inspectedFeedbackForm) {
                $evaluationReportSummaryFilter->addFeedbackFormId($this->stripTagsVariable($inspectedFeedbackForm['id']));
            }
        }
        return $evaluationReportSummaryFilter;
    }
    
}
