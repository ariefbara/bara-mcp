<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class ShowEvaluationReportTask implements ITaskExecutableByParticipant
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
     * @var EvaluationReport | null
     */
    public $result;

    public function __construct(EvaluationReportRepository $evaluationReportRepository, string $id)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
        $this->id = $id;
        $this->result = null;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->evaluationReportRepository
                ->anActiveEvaluationReportBelongsToParticipant($participantId, $this->id);
    }

}
