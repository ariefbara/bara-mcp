<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\ {
    Application\Service\Firm\Program\ViewParticipantSummary,
    Infrastructure\Persistence\Doctrine\Repository\DoctrineParticipantSummaryRepository
};

class ParticipantSummaryController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $searchByParticipantName = $this->stripTagQueryRequest('searchByParticipantName');
        $service = $this->buildViewService();
        $participantSummaries = $service
                ->showAll($programId, $this->getPage(), $this->getPageSize(), $searchByParticipantName);
        
        $result = [];
        $result["total"] = $service->getTotalActvieParticipants($programId, $searchByParticipantName);
        foreach ($participantSummaries as $participantSummary) {
            $result["list"][] = [
                "id" => $participantSummary["participantId"],
                "name" => $participantSummary["participantName"],
                "participantRating" => $participantSummary["participantRating"],
                "totalCompletedMission" => $participantSummary["totalCompletedMission"],
                "totalMission" => $participantSummary["totalMission"],
                "lastCompletedTime" => $participantSummary["lastCompletedTime"],
                "lastMissionId" => $participantSummary["lastMissionId"],
                "lastMissionName" => $participantSummary["lastMissionName"],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function showAllMetricAchievement($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        
        $orderType = $this->filterBooleanOfQueryRequest("ascOrder")? "ASC": "DESC";
        $participants = $service->showAllWithMetricAchievement(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $orderType);
        $result = [];
        $result["total"] = $service->getTotalActvieParticipants($programId, null);
        foreach ($participants as $participant) {
            $result["list"][] = [
                "id" => $participant["participantId"],
                "name" => $participant["participantName"],
                "achievement" => $participant["achievement"],
                "completedMetric" => $participant["completedMetric"],
                "totalAssignedMetric" => $participant["totalAssignedMetric"],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function showAllEvaluationSummary($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $participants = $service->showAllWithEvaluationSummary(
            $this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = $service->getTotalActvieParticipants($programId);
        foreach ($participants as $participant) {
            $result["list"][] = [
                "id" => $participant["participantId"],
                "name" => $participant["participantName"],
                "evaluationPlanId" => $participant["evaluationPlanId"],
                "evaluationPlanName" => $participant["evaluationPlanName"],
                "scheduledEvaluation" => $participant["scheduledEvaluation"],
                "extendDays" => $participant["extendDays"],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function buildViewService()
    {
        $participantSummaryRepository = new DoctrineParticipantSummaryRepository($this->em);
        return new ViewParticipantSummary($participantSummaryRepository);
    }
}
