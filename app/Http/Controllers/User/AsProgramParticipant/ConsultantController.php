<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use Query\ {
    Application\Service\Firm\Program\ViewConsultant,
    Domain\Model\Firm\Program\Consultant
};

class ConsultantController extends AsProgramParticipantBaseController
{
    public function showAll($firmId, $programId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $viewService = $this->buildViewService();
        $consultants = $viewService->showAll($firmId, $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($consultants);
        foreach ($consultants as $consultant) {
            $result['list'][] = $this->arrayDataOfConsultant($consultant);
        }
        return $this->listQueryResponse($result);
        
    }
    public function show($firmId, $programId, $consultantId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $viewService = $this->buildViewService();
        $consultant = $viewService->showById($firmId, $programId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
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
