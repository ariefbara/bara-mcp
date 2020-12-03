<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\ {
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    Personnel\PersonnelBaseController
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramCoordinator\ViewEvaluationReport,
    Domain\Model\Firm\Program\EvaluationPlan\EvaluationReport
};
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Service\FileInfoBelongsToPersonnelFinder
};
use User\ {
    Application\Service\Personnel\Coordinator\SubmitEvaluationReport,
    Domain\DependencyModel\Firm\Program\EvaluationPlan,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\Model\Personnel\Coordinator
};

class EvaluationReportController extends PersonnelBaseController
{

    public function submit($coordinatorId, $participantId, $evaluationPlanId)
    {
        $service = $this->buildSubmitService();
        
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToPersonnelFinder($fileInfoRepository, $this->firmId(), $this->personnelId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $service->execute($this->firmId(), $this->personnelId(), $coordinatorId, $participantId, $evaluationPlanId,
                $formRecordData);

        return $this->show($participantId, $evaluationPlanId);
    }

    public function show($participantId, $evaluationPlanId)
    {
        $service = $this->buildViewService();
        $evaluationReport = $service->showById($this->firmId(), $this->personnelId(), $participantId, $evaluationPlanId);
        return $this->singleQueryResponse($this->arrayDataofEvaluationReport($evaluationReport));
    }

    public function showAll($coordinatorId)
    {
        $evaluationReports = $this->buildViewService()
                ->showAll($this->firmId(), $this->personnelId(), $coordinatorId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($evaluationReports);
        foreach ($evaluationReports as $evaluationReport) {
            $result["list"][] = [
                "id" => $evaluationReport->getId(),
                "submitTime" => $evaluationReport->getSubmitTimeString(),
                "participant" => [
                    "id" => $evaluationReport->getParticipant()->getId(),
                    "name" => $evaluationReport->getParticipant()->getName(),
                ],
                "evaluationPlan" => [
                    "id" => $evaluationReport->getEvaluationPlan()->getId(),
                    "name" => $evaluationReport->getEvaluationPlan()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataofEvaluationReport(EvaluationReport $evaluationReport): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($evaluationReport);
        $result["id"] = $evaluationReport->getId();
        $result["participant"] = [
            "id" => $evaluationReport->getParticipant()->getId(),
            "name" => $evaluationReport->getParticipant()->getName(),
        ];
        $result["evaluationPlan"] = [
            "id" => $evaluationReport->getEvaluationPlan()->getId(),
            "name" => $evaluationReport->getEvaluationPlan()->getName(),
        ];
        return $result;
    }

    protected function buildViewService()
    {
        $evaluationReportRepository = $this->em->getRepository(EvaluationReport::class);
        return new ViewEvaluationReport($evaluationReportRepository);
    }

    protected function buildSubmitService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);

        return new SubmitEvaluationReport($coordinatorRepository, $participantRepository, $evaluationPlanRepository);
    }

}
