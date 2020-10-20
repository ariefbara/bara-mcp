<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\ {
    Application\Service\Firm\Program\ViewConsultantSummary,
    Infrastructure\Persistence\Doctrine\Repository\DoctrineConsultantSummaryRepository
};

class ConsultantSummaryController extends AsProgramCoordinatorBaseController
{
    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $service = $this->buildViewService();
        $consultantSummaries = $service->showAll($programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = $service->getTotalActiveConsultant($programId);
        foreach ($consultantSummaries as $consultantSummary) {
            $result["list"][] = [
                "id" => $consultantSummary["id"],
                "name" => $consultantSummary["name"],
                "consultationRequest" => [
                    "total" => $consultantSummary["consultationRequestCount"],
                    "unconcluded" => $consultantSummary["unconcludedConsultationRequestCount"],
                    "accepted" => $consultantSummary["consultationSessionCount"],
                    "rejected" => $consultantSummary["rejectedConsultationRequestCount"],
                    "cancelled" => $consultantSummary["cancelledConsultationRequestCount"],
                ],
                "comment" => [
                    "total" => $consultantSummary["commentCount"],
                    "lastSevenDaysCount" => $consultantSummary["commentCountInLastSevenDays"],
                    "lastSubmitTime" => $consultantSummary["lastSubmitTime"],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function buildViewService()
    {
        $consultantSummaryRepository = new DoctrineConsultantSummaryRepository($this->em);
        return new ViewConsultantSummary($consultantSummaryRepository);
    }
}
