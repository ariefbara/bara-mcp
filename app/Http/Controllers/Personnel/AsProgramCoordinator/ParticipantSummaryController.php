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
        
        $service = $this->buildViewService();
        
        $participantSummaries = $service->showAll($programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = $service->getTotalActvieParticipants($programId);
        foreach ($participantSummaries as $participantSummary) {
            $result["list"][] = [
                "id" => $participantSummary["participantId"],
                "name" => $participantSummary["participantName"],
                "totalCompletedMission" => $participantSummary["totalCompletedMission"],
                "totalMission" => $participantSummary["totalMission"],
                "lastCompletedTime" => $participantSummary["lastCompletedTime"],
                "lastMissionId" => $participantSummary["lastMissionId"],
                "lastMissionName" => $participantSummary["lastMissionName"],
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
