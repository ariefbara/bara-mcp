<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Task\Dependency\Firm\ClientRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;

class GenerateFirmEvaluationReportTranscriptTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ClientRepository
     */
    protected $clientRepository;

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var EvaluationReportSummaryFilter
     */
    protected $evaluationReportSummaryFilter;

    /**
     * 
     * @var ClientEvaluationReportTranscriptResult
     */
    protected $clientEvaluationReportTranscriptResult;

    public function __construct(ClientRepository $clientRepository,
            EvaluationReportRepository $evaluationReportRepository,
            EvaluationReportSummaryFilter $evaluationReportSummaryFilter,
            ClientEvaluationReportTranscriptResult $clientEvaluationReportTranscriptResult)
    {
        $this->clientRepository = $clientRepository;
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->evaluationReportSummaryFilter = $evaluationReportSummaryFilter;
        $this->clientEvaluationReportTranscriptResult = $clientEvaluationReportTranscriptResult;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $clients = $this->clientRepository
                ->allNonPaginatedActiveClientInFirm($firm, $this->evaluationReportSummaryFilter->getClientIdList());
        foreach ($clients as $client) {
            $this->clientEvaluationReportTranscriptResult->addTranscriptTableForClient($client);
        }
        
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedEvaluationReportsInFirm($firm, $this->evaluationReportSummaryFilter);
        foreach ($evaluationReports as $evaluationReport) {
            $this->clientEvaluationReportTranscriptResult->includeEvaluationReport($evaluationReport);
        }
    }

}
