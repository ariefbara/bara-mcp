<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\PaginationPayload;

class ShowAllMentorEvaluationReportsTask implements ITaskExecutableByParticipant
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
    
    public function execute(string $participantId): void
    {
        $this->result = $this->evaluationReportRepository
                ->allActiveEvaluationReportsBelongsToParticipant(
                        $participantId, $this->payload->getPage(), $this->payload->getPageSize());
    }

}
