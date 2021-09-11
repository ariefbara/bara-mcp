<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class ShowActiveEvaluationReportTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var ?EvaluationReport
     */
    public $result;

    public function __construct(EvaluationReportRepository $evaluationReportRepository, string $id)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->id = $id;
        $this->result = null;
    }

    public function execute(string $clientId): void
    {
        $this->result = $this->evaluationReportRepository
                ->anActiveEvaluationReportCorrespondWithClient($clientId, $this->id);
    }

}
