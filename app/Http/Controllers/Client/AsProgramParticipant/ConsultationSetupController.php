<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\ {
    Application\Service\Firm\Program\ConsultationSetupView,
    Domain\Model\Firm\Program\ConsultationSetup
};

class ConsultationSetupController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $consultationSetupId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        $consultationSetup = $service->showById($programCompositionId, $consultationSetupId);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultationSetup($consultationSetup));
    }

    public function showAll($firmId, $programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        
        $consultationSetups = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
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
        return new ConsultationSetupView($consultationSetupRepository);
    }

}
