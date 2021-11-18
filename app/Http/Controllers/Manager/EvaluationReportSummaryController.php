<?php

namespace App\Http\Controllers\Manager;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Query\Domain\Task\InFirm\ClientEvaluationReportSummaryResult;
use Query\Domain\Task\InFirm\ClientEvaluationReportTranscriptResult;
use Query\Domain\Task\InFirm\GenerateFirmEvaluationReportSummaryTask;
use Query\Domain\Task\InFirm\GenerateFirmEvaluationReportTranscriptTask;

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
        return $evaluationReportSummaryFilter;
    }

}
