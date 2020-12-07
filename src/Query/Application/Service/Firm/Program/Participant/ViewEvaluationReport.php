<?php

namespace Query\Application\Service\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;

class ViewEvaluationReport
{

    /**
     *
     * @var EvaluationReportRepository
     */
    protected $evaluationReportRepository;

    function __construct(EvaluationReportRepository $evaluationReportRepository)
    {
        $this->evaluationReportRepository = $evaluationReportRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @return EvaluationReport[]
     */
    public function showAll(
            string $firmId, string $programId, string $participantId, int $page, int $pageSize,
            ?string $evaluationPlanId)
    {
        return $this->evaluationReportRepository->allEvaluationReportsBelongsToProgramParticipant(
                $firmId, $programId, $participantId, $page, $pageSize, $evaluationPlanId);
    }

    public function showById(string $firmId, string $programId, string $evaluationReportId): EvaluationReport
    {
        return $this->evaluationReportRepository->anEvaluationReportInProgram($firmId, $programId, $evaluationReportId);
    }

}
