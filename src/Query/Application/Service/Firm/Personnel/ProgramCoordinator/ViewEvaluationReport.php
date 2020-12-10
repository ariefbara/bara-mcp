<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

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
     * @param string $personnelId
     * @param string $coordinatorId
     * @param int $page
     * @param int $pageSize
     * @return EvaluationReport[]
     */
    public function showAll(string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize)
    {
        return $this->evaluationReportRepository->allEvaluationReportsBelongsToCoordinator(
                        $firmId, $personnelId, $coordinatorId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $participantId, string $evaluationPlanId): EvaluationReport
    {
        return $this->evaluationReportRepository->anEvaluationReportBelongsToPersonnel(
                $firmId, $personnelId, $participantId, $evaluationPlanId);
    }

}
