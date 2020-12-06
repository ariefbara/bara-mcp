<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Evaluation;

class ViewEvaluation
{

    /**
     *
     * @var EvaluationRepository
     */
    protected $evaluationRepository;

    function __construct(EvaluationRepository $evaluationRepository)
    {
        $this->evaluationRepository = $evaluationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $participantId
     * @param int $page
     * @param int $pageSize
     * @return Evaluation[]
     */
    public function showAll(string $firmId, string $programId, string $participantId, int $page, int $pageSize)
    {
        return $this->evaluationRepository
                        ->allEvaluationsOfParticipant($firmId, $programId, $participantId, $page, $pageSize);
    }

    public function showById(string $firmId, string $programId, string $evaluationId): Evaluation
    {
        return $this->evaluationRepository->anEvaluationOfInProgram($firmId, $programId, $evaluationId);
    }

}
