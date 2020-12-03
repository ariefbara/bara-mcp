<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\ {
    Application\Service\Firm\Program\ViewEvaluationPlan,
    Domain\Model\Firm\Program\EvaluationPlan
};

class EvaluationPlanController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $evaluationPlans = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($evaluationPlans);
        foreach ($evaluationPlans as $evaluationPlan) {
            $result["list"][] = [
                "id" => $evaluationPlan->getId(),
                "name" => $evaluationPlan->getName(),
                "interval" => $evaluationPlan->getInterval(),
                "reportForm" => [
                    "id" => $evaluationPlan->getReportForm()->getId(),
                    "name" => $evaluationPlan->getReportForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($programId, $evaluationPlanId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $evaluationPlan = $this->buildViewService()->showByIdInProgram($this->firmId(), $programId, $evaluationPlanId);
        return $this->singleQueryResponse($this->arrayDataOfEvaluationPlan($evaluationPlan));
    }
    
    protected function arrayDataOfEvaluationPlan(EvaluationPlan $evaluationPlan): array
    {
        $reportForm = (new FormToArrayDataConverter())->convert($evaluationPlan->getReportForm());
        $reportForm["id"] = $evaluationPlan->getReportForm()->getId();
        return [
            "id" => $evaluationPlan->getId(),
            "name" => $evaluationPlan->getName(),
            "interval" => $evaluationPlan->getInterval(),
            "reportForm" => $reportForm,
        ];
    }
    
    protected function buildViewService()
    {
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);
        return new ViewEvaluationPlan($evaluationPlanRepository);
    }
}
