<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator\Participant;

use App\Http\Controllers\Personnel\AsProgramCoordinator\AsProgramCoordinatorBaseController;
use Firm\Application\Service\Coordinator\EvaluateParticipant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\Participant;
use Query\Application\Service\Firm\Program\Participant\ViewEvaluation;
use Query\Domain\Model\Firm\Program\Participant\Evaluation;

class EvaluationController extends AsProgramCoordinatorBaseController
{

    public function evaluate($programId, $participantId)
    {
        $service = $this->buildEvaluationService();
        $evaluationPlanId = $this->stripTagsInputRequest("evaluationPlanId");
        $status = $this->stripTagsInputRequest("status");
        $extendDays = $this->integerOfInputRequest("extendDays");
        $evaluationData = new Participant\EvaluationData($status, $extendDays);

        $service->execute(
                $this->firmId(), $this->personnelId(), $programId, $participantId, $evaluationPlanId, $evaluationData);
        
        return $this->commandOkResponse();
    }

    public function showAll($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $service = $this->buildViewService();
        $evaluations = $service->showAll(
                $this->firmId(), $programId, $participantId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($evaluations);
        foreach ($evaluations as $evaluation) {
            $result["list"][] = $this->arrayDataOfEvaluation($evaluation);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $evaluationId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $service = $this->buildViewService();
        $evaluation = $service->showById($this->firmId(), $programId, $evaluationId);
        return $this->singleQueryResponse($this->arrayDataOfEvaluation($evaluation));
    }

    protected function arrayDataOfEvaluation(Evaluation $evaluation): array
    {
        return [
            "id" => $evaluation->getId(),
            "submitTime" => $evaluation->getSubmitTimeString(),
            "status" => $evaluation->getStatus(),
            "extendDays" => $evaluation->getExtendDays(),
            "evaluationPlan" => [
                "id" => $evaluation->getEvaluationPlan()->getId(),
                "name" => $evaluation->getEvaluationPlan()->getName(),
            ],
            "coordinator" => [
                "id" => $evaluation->getCoordinator()->getId(),
                "name" => $evaluation->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $evaluationRepository = $this->em->getRepository(Evaluation::class);
        return new ViewEvaluation($evaluationRepository);
    }

    protected function buildEvaluationService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $evaluationPlanRepository = $this->em->getRepository(EvaluationPlan::class);

        return new EvaluateParticipant($coordinatorRepository, $participantRepository, $evaluationPlanRepository);
    }

}
