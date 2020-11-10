<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\ {
    Application\Service\Firm\Program\CoordinatorView,
    Domain\Model\Firm\Program\Coordinator
};

class CoordinatorController extends AsProgramParticipantBaseController
{
    public function show($programId, $coordinatorId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $coordinator = $service->showById($programCompositionId, $coordinatorId);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinator($coordinator));
    }
    public function showAll($programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $coordinators = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($coordinators);
        foreach ($coordinators as $coordinator) {
            $result["list"][] = $this->arrayDataOfCoordinator($coordinator);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfCoordinator(Coordinator $coordinator): array
    {
        return [
            "id" => $coordinator->getId(),
            "personnel" => [
                "id" => $coordinator->getPersonnel()->getId(),
                "name" => $coordinator->getPersonnel()->getName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new CoordinatorView($coordinatorRepository);
    }
}
