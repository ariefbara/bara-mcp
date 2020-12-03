<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Query\ {
    Application\Service\Firm\Program\ViewConsultant,
    Domain\Model\Firm\Program\Consultant
};

class ConsultantController extends AsProgramConsultantBaseController
{

    public function show($programId, $consultantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $viewService = $this->buildViewService();
        $consultant = $viewService->showById($this->firmId(), $programId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function showAll($programId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $viewService = $this->buildViewService();
        $consultants = $viewService->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($consultants);
        foreach ($consultants as $consultant) {
            $result["list"][] = $this->arrayDataOfConsultant($consultant);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultant(Consultant $consultant): array
    {
        return [
            "id" => $consultant->getId(),
            "personnel" => [
                "id" => $consultant->getPersonnel()->getId(),
                "name" => $consultant->getPersonnel()->getName(),
            ],
        ];
    }
    
    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new ViewConsultant($consultantRepository);
    }

}
