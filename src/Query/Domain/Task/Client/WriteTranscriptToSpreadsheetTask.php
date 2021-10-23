<?php

namespace Query\Domain\Task\Client;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\TranscriptTable;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class WriteTranscriptToSpreadsheetTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var Spreadsheet
     */
    protected $spreadsheet;

    /**
     * 
     * @var bool
     */
    protected $summaryStyleView;

    /**
     * 
     * @var ClientTranscriptTableCollection
     */
    protected $clientTranscriptTableCollection;

    public function __construct(
            EvaluationReportRepository $evaluationReportRepository, Spreadsheet $spreadsheet, bool $summaryStyleView)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->spreadsheet = $spreadsheet;
        $this->summaryStyleView = $summaryStyleView;
        $this->clientTranscriptTableCollection = new ClientTranscriptTableCollection();
    }

    public function execute(string $clientId): void
    {
        
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedActiveEvaluationReportCorrespondWithClient($clientId);
        foreach ($evaluationReports as $evaluationReport) {
            $this->clientTranscriptTableCollection->include($evaluationReport);
        }
        
        $this->clientTranscriptTableCollection->saveToSpreadsheet($this->spreadsheet, $this->summaryStyleView);
    }

}
