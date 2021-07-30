<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\FormToArrayDataConverter;
use App\Http\Controllers\Personnel\ProgramConsultation\ProgramConsultationBaseController;
use Query\Application\Service\Consultant\ViewEvaluationPlan;
use Query\Domain\Model\Firm\Program\Consultant;
use Query\Domain\Model\Firm\Program\EvaluationPlan;
use Query\Domain\Model\Firm\Program\Mission;

class EvaluationPlanController extends ProgramConsultationBaseController
{
    public function showAll($programConsultationId)
    {
        $disabledStatus = $this->filterBooleanOfVariable($this->request->query('disabled'));
        $evaluationPlans = $this->buildViewService()->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize(), 
                $disabledStatus);
        
        $result = [];
        $result['total'] = count($evaluationPlans);
        foreach ($evaluationPlans as $evaluationPlan) {
            $result['list'][] = [
                "id" => $evaluationPlan->getId(),
                "name" => $evaluationPlan->getName(),
                "interval" => $evaluationPlan->getInterval(),
                "disabled" => $evaluationPlan->isDisabled(),
                "reportForm" => [
                    "id" => $evaluationPlan->getReportForm()->getId(),
                    "name" => $evaluationPlan->getReportForm()->getName(),
                ],
                "mission" => $this->arrayDataOfMission($evaluationPlan->getMission()),
            ];
        }
        return $result;
    }
    
    public function show($programConsultationId, $evaluationPlanId)
    {
        $evaluationPlan = $this->buildViewService()->showById(
                $this->firmId(), $this->personnelId(), $programConsultationId, $evaluationPlanId);
        return $this->arrayDataOfEvaluationPlan($evaluationPlan);
    }
    
    protected function arrayDataOfEvaluationPlan(EvaluationPlan $evaluationPlan): array
    {
        $reportForm = (new FormToArrayDataConverter())->convert($evaluationPlan->getReportForm());
        $reportForm["id"] = $evaluationPlan->getReportForm()->getId();
        return [
            "id" => $evaluationPlan->getId(),
            "name" => $evaluationPlan->getName(),
            "interval" => $evaluationPlan->getInterval(),
            "disabled" => $evaluationPlan->isDisabled(),
            "reportForm" => $reportForm,
            "mission" => $this->arrayDataOfMission($evaluationPlan->getMission()),
        ];
    }
    
    protected function arrayDataOfMission(?Mission $mission): ?array
    {
        return empty($mission) ? null: [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
        ];
    }
    
    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);
        return new ViewEvaluationPlan($consultantRepository, $evaluationPlanRepository);
    }
}
