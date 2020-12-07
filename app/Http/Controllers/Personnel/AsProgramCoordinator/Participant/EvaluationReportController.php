<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator\Participant;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\Personnel\AsProgramCoordinator\AsProgramCoordinatorBaseController;
use Query\Application\Service\Firm\Program\Participant\ViewEvaluationReport;
use Query\Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport;

class EvaluationReportController extends AsProgramCoordinatorBaseController
{

    public function showAll($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $evaluationPlanId = $this->stripTagQueryRequest("evaluationPlanId");
        $evaluationReports = $service->showAll(
                $this->firmId(), $programId, $participantId, $this->getPage(), $this->getPageSize(), $evaluationPlanId);
        
        $result = [];
        $result["total"] = count($evaluationReports);
        foreach ($evaluationReports as $evaluationReport) {
            $result["list"][] = [
                "id" => $evaluationReport->getId(),
                "coordinator" => [
                    "id" => $evaluationReport->getCoordinator()->getId(),
                    "name" => $evaluationReport->getCoordinator()->getPersonnel()->getName(),
                ],
                "evaluationPlan" => [
                    "id" => $evaluationReport->getEvaluationPlan()->getId(),
                    "name" => $evaluationReport->getEvaluationPlan()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $evaluationReportId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $evaluationReport = $service->showById($this->firmId(), $programId, $evaluationReportId);
        return $this->singleQueryResponse($this->arrayDataOfEvaluationReport($evaluationReport));
    }

    protected function arrayDataOfEvaluationReport(EvaluationReport $evaluationReport): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($evaluationReport);
        $result["id"] = $evaluationReport->getId();
        $result["evaluationPlan"] = [
            "id" => $evaluationReport->getEvaluationPlan()->getId(),
            "name" => $evaluationReport->getEvaluationPlan()->getName(),
        ];
        $result["coordinator"] = [
            "id" => $evaluationReport->getCoordinator()->getId(),
            "name" => $evaluationReport->getCoordinator()->getPersonnel()->getName(),
        ];
        return $result;
    }

    protected function buildViewService()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        return new ViewEvaluationReport($evaluationReportRepository);
    }

}
