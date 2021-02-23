<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Application\Service\Firm\Program\MissionAddBranch;
use Firm\Application\Service\Firm\Program\MissionAddRoot;
use Firm\Application\Service\Firm\Program\MissionPublish;
use Firm\Application\Service\Firm\Program\MissionUpdate;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\ChangeMissionWorksheetForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\WorksheetForm;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Application\Service\Firm\Program\ViewMission;
use Query\Domain\Model\Firm\Program\Mission as Mission2;

class MissionController extends ManagerBaseController
{

    public function addRoot($programId)
    {
        $service = $this->buildAddRootService();
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        
        $missionId = $service->execute(
                $this->firmId(), $programId, $name, $description, $worksheetFormId, $position);
        
        $viewService = $this->buildViewService();
        $mission = $viewService->showById($this->firmId(), $programId, $missionId);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function addBranch($programId, $missionId)
    {
        $service = $this->buildAddBranchService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        
        $branchId = $service->execute(
                $programCompositionId, $missionId, $name, $description, $worksheetFormId, $position);
        
        $viewService = $this->buildViewService();
        $mission = $viewService->showById($this->firmId(), $programId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function update($programId, $missionId)
    {
        $service = $this->buildUpdateService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        
        $service->execute($programCompositionId, $missionId, $name, $description, $position);
        return $this->show($programId, $missionId);
    }
    
    public function changeWorksheetForm($programId, $missionId)
    {
        $worksheetFormId = $this->stripTagsInputRequest("worksheetFormId");
        $this->buildChangeChangeWorksheetFormService()
                ->execute($this->firmId(), $this->managerId(), $missionId, $worksheetFormId);
        
        return $this->show($programId, $missionId);
    }
    
    public function publish($programId, $missionId)
    {
        $service = $this->buildPublishService();
        
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $service->execute($programCompositionId, $missionId);
        return $this->show($programId, $missionId);
    }

    public function show($programId, $missionId)
    {
        $service = $this->buildViewService();
        $mission = $service->showById($this->firmId(), $programId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $missions = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize(), false);
        
        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $result['list'][] = $this->arrayDataOfMission($mission, $programId);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfMission(Mission2 $mission, string $programId): array
    {
        $learningMaterialRepository = $this->em->getRepository(Mission2\LearningMaterial::class);
        $learningMaterials = (new ViewLearningMaterial($learningMaterialRepository))
                ->showAll($this->firmId(), $programId, $mission->getId(), 1, 100);
        $learningMaterialResult = [];
        foreach ($learningMaterials as $learningMaterial) {
            $learningMaterialResult[] = $this->arrayDataOfLearningMaterial($learningMaterial);
        }
        
        $parentData = empty($mission->getParent())? null: 
            [
              "id" => $mission->getParent()->getId(),  
              "name" => $mission->getParent()->getName(),  
            ];
        return [
            "parent" => $parentData,
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            "published" => $mission->isPublished(),
            "worksheetForm" => [
                "id" => $mission->getWorksheetForm()->getId(),
                "name" => $mission->getWorksheetForm()->getName(),
            ],
            'learningMaterials' => $learningMaterialResult,
        ];
    }
    protected function arrayDataOfLearningMaterial(Mission2\LearningMaterial $learningMaterial): array
    {
        return [
            'id' => $learningMaterial->getId(),
            'name' => $learningMaterial->getName(),
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
        $missionRepository = $this->em->getRepository(Mission2::class);
        return new ViewMission($missionRepository);
    }
    protected function buildChangeChangeWorksheetFormService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        
        return new ChangeMissionWorksheetForm($missionRepository, $managerRepository, $worksheetFormRepository);
    }

}
