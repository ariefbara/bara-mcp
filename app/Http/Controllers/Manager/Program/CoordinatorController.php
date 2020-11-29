<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\CoordinatorAssign,
    Application\Service\Firm\Program\CoordinatorRemove,
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Manager\DisableCoordinator,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Coordinator
};
use Query\ {
    Application\Service\Firm\Program\CoordinatorView,
    Domain\Model\Firm\Program\Coordinator as Coordinator2
};

class CoordinatorController extends ManagerBaseController
{

    public function assign($programId)
    {
        $service = $this->buildAssignService();
        $personnelId = $this->stripTagsInputRequest('personnelId');
        $coordinatorId = $service->execute($this->firmId(), $programId, $personnelId);
        
        $viewService = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $coordinator = $viewService->showById($programCompositionId, $coordinatorId);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinator($coordinator));
    }

    public function disable($programId, $coordinatorId)
    {
        $service = $this->buildDisableService();
        $service->execute($this->firmId(), $this->managerId(), $coordinatorId);
        return $this->commandOkResponse();
    }

    public function show($programId, $coordinatorId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $coordinator = $service->showById($programCompositionId, $coordinatorId);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinator($coordinator));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $coordinators = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($coordinators);
        foreach ($coordinators as $coordinator) {
            $result['list'][] = $this->arrayDataOfCoordinator($coordinator);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfCoordinator(Coordinator2 $coordinator)
    {
        return [
            "id" => $coordinator->getId(),
            "personnel" => [
                "id" => $coordinator->getPersonnel()->getId(),
                "name" => $coordinator->getPersonnel()->getName(),
            ],
        ];
        
    }

    protected function buildAssignService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $personnelRepository = $this->em->getRepository(Personnel::class);
        return new CoordinatorAssign($programRepository, $personnelRepository);
    }

    protected function buildViewService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator2::class);
        return new CoordinatorView($coordinatorRepository);
    }
    
    protected function buildDisableService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new DisableCoordinator($coordinatorRepository, $managerRepository);
    }

}
