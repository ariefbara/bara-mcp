<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\MissionAddBranch,
    Application\Service\Firm\Program\MissionAddRoot,
    Application\Service\Firm\Program\MissionPublish,
    Application\Service\Firm\Program\MissionUpdate,
    Application\Service\Firm\Program\MissionView,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm
};

class MissionController extends ManagerBaseController
{

    public function addRoot($programId)
    {
        $service = $this->buildAddRootService();
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        
        $mission = $service->execute(
                $this->firmId(), $programId, $name, $description, $worksheetFormId, $position);
        
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission));
    }

    public function addBranch($programId, $missionId)
    {
        $service = $this->buildAddBranchService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        
        $mission = $service->execute(
                $programCompositionId, $missionId, $name, $description, $worksheetFormId, $position);
        
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission));
    }

    public function update($programId, $missionId)
    {
        $service = $this->buildUpdateService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        
        $mission = $service->execute($programCompositionId, $missionId, $name, $description, $position);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    public function publish($programId, $missionId)
    {
        $service = $this->buildPublishService();
        
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $mission = $service->execute($programCompositionId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }

    public function show($programId, $missionId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $mission = $service->showById($programCompositionId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $missions = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $previousMission = empty($mission->getPreviousMission())? null: 
                [
                  "id" => $mission->getPreviousMission()->getId(),  
                  "name" => $mission->getPreviousMission()->getName(),  
                ];
            $result['list'][] = [
                "previousMission" => $previousMission,
                "id" => $mission->getId(),
                "name" => $mission->getName(),
                "position" => $mission->getPosition(),
                "published" => $mission->isPublished(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfMission(Mission $mission): array
    {
        $previousMission = empty($mission->getPreviousMission())? null: 
            [
              "id" => $mission->getPreviousMission()->getId(),  
              "name" => $mission->getPreviousMission()->getName(),  
            ];
        return [
            "previousMission" => $previousMission,
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            "published" => $mission->isPublished(),
            "worksheetForm" => [
                "id" => $mission->getWorksheetForm()->getId(),
                "name" => $mission->getWorksheetForm()->getName(),
            ],
        ];
    }
    
    protected function buildAddRootService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $programRepository = $this->em->getRepository(Program::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new MissionAddRoot($missionRepository, $programRepository, $worksheetFormRepository);
    }
    protected function buildAddBranchService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        
        return new MissionAddBranch($missionRepository, $worksheetFormRepository);
    }
    protected function buildUpdateService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new MissionUpdate($missionRepository);
    }
    
    protected function buildPublishService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new MissionPublish($missionRepository);
    }
    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new MissionView($missionRepository);
    }

}
