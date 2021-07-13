<?php

namespace Query\Application\Service\Consultant;

use Query\Domain\Model\Firm\Program\EvaluationPlan;

class ViewEvaluationPlan
{

    /**
     * 
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     * 
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    public function __construct(
            ConsultantRepository $consultantRepository, EvaluationPlanRepository $evaluationPlanRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->evaluationPlanRepository = $evaluationPlanRepository;
    }

    public function showAll(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize, ?bool $disabledStatus)
    {
        return $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                        ->viewAllEvaluationPlan($this->evaluationPlanRepository, $page, $pageSize, $disabledStatus);
    }
    
    public function showById(string $firmId, string $personnelId, string $consultantId, string $evaluationPlanId): EvaluationPlan
    {
        return $this->consultantRepository->aConsultantBelongsToPersonnel($firmId, $personnelId, $consultantId)
                ->viewEvaluationPlan($this->evaluationPlanRepository, $evaluationPlanId);
    }

}
