<?php

namespace Firm\Application\Service\Manager;

class DisableEvaluationPlan
{
    /**
     *
     * @var EvaluationPlanRepository
     */
    protected $evaluationPlanRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;
    
    function __construct(EvaluationPlanRepository $evaluationPlanRepository, ManagerRepository $managerRepository)
    {
        $this->evaluationPlanRepository = $evaluationPlanRepository;
        $this->managerRepository = $managerRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $evaluationPlanId): void
    {
        $evaluationPlan = $this->evaluationPlanRepository->ofId($evaluationPlanId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->disableEvaluationPlan($evaluationPlan);
        
        $this->evaluationPlanRepository->update();
    }

}
