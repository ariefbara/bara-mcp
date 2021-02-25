<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Firm\Application\Service\Manager\ChangeMissionWorksheetForm;
use Firm\Application\Service\Manager\CreateBranchMission;
use Firm\Application\Service\Manager\CreateRootMission;
use Firm\Application\Service\Manager\PublishMission;
use Firm\Application\Service\Manager\UpdateMission;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\WorksheetForm;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Application\Service\Firm\Program\ViewMission;
use Query\Domain\Model\Firm\Program\Mission as Mission2;

class MissionController extends ManagerBaseController
{
    
    protected function getMissionData()
    {
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        return new Program\MissionData($name, $description, $position);
    }

    public function addRoot($programId)
    {
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        $missionId = $this->buildAddRootService()
                ->execute($this->firmId(), $this->managerId(), $programId, $worksheetFormId, $this->getMissionData());
        
        $mission = $this->buildViewService()->showById($this->firmId(), $programId, $missionId);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function addBranch($programId, $missionId)
    {
        $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
        $branchId = $this->buildAddBranchService()
                ->execute($this->firmId(), $this->managerId(), $missionId, $worksheetFormId, $this->getMissionData());
        
        $mission = $this->buildViewService()->showById($this->firmId(), $programId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function update($programId, $missionId)
    {
        $this->buildUpdateService()->execute($this->firmId(), $this->managerId(), $missionId, $this->getMissionData());
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
        $this->buildPublishService()->execute($this->firmId(), $this->managerId(), $missionId);
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
        $managerRepository = $this->em->getRepository(Manager::class);
        $programRepository = $this->em->getRepository(Program::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new CreateRootMission($managerRepository, $programRepository, $worksheetFormRepository, $missionRepository);
    }
    protected function buildAddBranchService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new CreateBranchMission($managerRepository, $missionRepository, $worksheetFormRepository);
    }
    protected function buildUpdateService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new UpdateMission($managerRepository, $missionRepository);
    }
    
    protected function buildPublishService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new PublishMission($managerRepository, $missionRepository);
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
