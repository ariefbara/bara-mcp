<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\CoordinatorAssign,
    Application\Service\Firm\Program\CoordinatorRemove,
    Application\Service\Firm\Program\CoordinatorView,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Coordinator
};

class CoordinatorController extends ManagerBaseController
{

    public function assign($programId)
    {
        $service = $this->buildAssignService();
        $personnelId = $this->stripTagsInputRequest('personnelId');
        $coordinator = $service->execute($this->firmId(), $programId, $personnelId);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinator($coordinator));
    }

    public function remove($programId, $coordinatorId)
    {
        $service = $this->buildRemoveService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $service->execute($programCompositionId, $coordinatorId);
        
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
    
    protected function arrayDataOfCoordinator(Coordinator $coordinator)
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

    protected function buildRemoveService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new CoordinatorRemove($coordinatorRepository);
    }

    protected function buildViewService()
    {
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new CoordinatorView($coordinatorRepository);
    }

}
