<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class GenerateTranscriptTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var array|null
     */
    public $result;
    
    /**
     * 
     * @var ClientTranscripTableCollection
     */
    protected $clientTranscriptTableCollection;

    public function __construct(EvaluationReportRepository $evaluationReportRepository)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->result = null;
        $this->clientTranscriptTableCollection = new ClientTranscriptTableCollection();
    }

    public function execute(string $clientId): void
    {
        $evaluationReports = $this->evaluationReportRepository
                ->allNonPaginatedActiveEvaluationReportCorrespondWithClient($clientId);
        foreach ($evaluationReports as $evaluationReport) {
            $this->clientTranscriptTableCollection->include($evaluationReport);
        }
        
        $this->result = $this->clientTranscriptTableCollection->toRelationalArray();
    }

}
