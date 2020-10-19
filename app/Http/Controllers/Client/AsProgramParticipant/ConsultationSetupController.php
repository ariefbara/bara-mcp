<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use Query\ {
    Application\Service\Firm\Program\ViewConsultationSetup,
    Domain\Model\Firm\Program\ConsultationSetup
};

class ConsultationSetupController extends AsProgramParticipantBaseController
{

    public function show($programId, $consultationSetupId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewService();
        $consultationSetup = $service->showById($this->firmId(), $programId, $consultationSetupId);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function showAll($programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewService();
        $consultationSetups = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        $result = [];
        $result['total'] = count($consultationSetups);
        foreach ($consultationSetups as $consultationSetup) {
            $result['list'][] = $this->arrayDataOfConsultationSetup($consultationSetup);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfConsultationSetup(ConsultationSetup $consultationSetup): array
    {
        return [
            "id" => $consultationSetup->getId(),
            "name" => $consultationSetup->getName(),
            "sessionDuration" => $consultationSetup->getSessionDuration(),
        ];
    }
    
    protected function buildViewService()
    {
        $consultationSetupRepository = $this->em->getRepository(ConsultationSetup::class);
        return new ViewConsultationSetup($consultationSetupRepository);
    }

}
