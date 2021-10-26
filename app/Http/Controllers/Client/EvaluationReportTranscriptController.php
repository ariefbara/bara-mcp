<?php

namespace App\Http\Controllers\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Client\GenerateTranscriptTask;
use Query\Domain\Task\Client\WriteTranscriptToSpreadsheetTask;

class EvaluationReportTranscriptController extends ClientBaseController
{
    public function show()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $task = new GenerateTranscriptTask($evaluationReportRepository);
        $this->executeQueryTask($task);
        
        return $this->singleQueryResponse($task->result);
    }
    
    public function downloadXls()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex($spreadsheet->getActiveSheetIndex());
        $summaryStyleView = $this->filterBooleanOfQueryRequest('summaryStyleView') ? true : false;
        
        $task = new WriteTranscriptToSpreadsheetTask($evaluationReportRepository, $spreadsheet, $summaryStyleView);
        $this->executeQueryTask($task);
        
        $writer = new Xlsx($spreadsheet);
        return $this->sendXlsDownloadResponse($writer, 'evaluation-report-transcript.xls');
    }
}
