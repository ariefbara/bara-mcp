<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\PaginationPayload;

class ShowAllActiveEvaluationReportsTask implements ITaskExecutableByClient
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    /**
     * 
     * @var PaginationPayload
     */
    protected $payload;

    /**
     * 
     * @var EvaluationReport[]
     */
    public $result;

    public function __construct(EvaluationReportRepository $evaluationReportRepository, PaginationPayload $payload)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->payload = $payload;
        $this->result = null;
    }

    public function execute(string $clientId): void
    {
        $this->result = $this->evaluationReportRepository
                ->allActiveEvaluationReportCorrespondWithClient(
                        $clientId, $this->payload->getPage(), $this->payload->getPageSize());
    }

}
