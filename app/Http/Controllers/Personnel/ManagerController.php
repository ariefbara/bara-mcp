<?php

namespace App\Http\Controllers\Personnel;

use Query\ {
    Application\Service\Firm\ManagerView,
    Domain\Model\Firm\Manager
};

class ManagerController extends PersonnelBaseController
{
    public function show($managerId)
    {
        $this->authorizedRequestFromActivePersonnel();

        $service = $this->buildViewService();
        $manager = $service->showById($this->firmId(), $managerId);
        
        return $this->singleQueryResponse($this->arrayDataOfManager($manager));
    }
    
    public function showAll()
    {
        $this->authorizedRequestFromActivePersonnel();
        
        $service = $this->buildViewService();
        $managers = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($managers);
        foreach ($managers as $manager) {
            $result["list"][] = $this->arrayDataOfManager($manager);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfManager(Manager $manager): array
    {
        return [
            "id" => $manager->getId(),
            "name" => $manager->getName(),
            "removed" => $manager->isRemoved(),
        ];
    }
    
    protected function buildViewService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        return new ManagerView($managerRepository);
    }
}
