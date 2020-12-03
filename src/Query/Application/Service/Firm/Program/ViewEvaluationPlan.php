<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\EvaluationPlan;

class ViewEvaluationPlan
{

    /**
     *
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    function __construct(EvaluationPlanRepository $evaluationPlanRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
    }

    /**
     * 
     * @param string $firmid
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $enableOnly
     * @return EvaluationPlan[]
     */
    public function showAll(string $firmid, string $programId, int $page, int $pageSize, ?bool $enableOnly = true)
    {
        return $this->evaluationPlanRepository
                        ->allEvaluationPlansInProgram($firmid, $programId, $page, $pageSize, $enableOnly);
    }

    public function showByIdInProgram(string $firmid, string $programId, string $evaluationPlanId): EvaluationPlan
    {
        return $this->evaluationPlanRepository->anEvaluationPlanInProgram($firmid, $programId, $evaluationPlanId);
    }

    public function showByIdInFirm(string $firmid, string $evaluationPlanId): EvaluationPlan
    {
        return $this->evaluationPlanRepository->anEvaluationPlanInFirm($firmid, $evaluationPlanId);
    }

}
