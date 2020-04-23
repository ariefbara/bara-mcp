<?php

namespace App\Http\Controllers\Manager\Program\Mission;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\Mission\LearningMaterialAdd,
    Application\Service\Firm\Program\Mission\LearningMaterialRemove,
    Application\Service\Firm\Program\Mission\LearningMaterialUpdate,
    Application\Service\Firm\Program\Mission\LearningMaterialView,
    Application\Service\Firm\Program\Mission\MissionCompositionId,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};

class LearningMaterialController extends ManagerBaseController
{

    public function add($programId, $missionId)
    {
        $service = $this->buildAddService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $name = $this->stripTagsInputRequest('name');
//        $content = $this->stripTagsInputRequest('content');
        $content = $this->request->input('content');//temporary disable strip tags to allow iframe
        $learningMaterial = $service->execute($programCompositionId, $missionId, $name, $content);
        
        return $this->commandCreatedResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function update($programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildUpdateService();
        $missionCompositionId = new MissionCompositionId($this->firmId(), $programId, $missionId);
        $name = $this->stripTagsInputRequest('name');
//        $content = $this->stripTagsInputRequest('content');
        $content = $this->request->input('content');//temporary disable strip tags to allow iframe
        $learningMaterial = $service->execute($missionCompositionId, $learningMaterialId, $name, $content);
        
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function remove($programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildRemoveService();
        $missionCompositionId = new MissionCompositionId($this->firmId(), $programId, $missionId);
        $service->execute($missionCompositionId, $learningMaterialId);
        
        return $this->commandOkResponse();
    }

    public function show($programId, $missionId, $learningMaterialId)
    {
        $service = $this->buildViewService();
        $missionCompositionId = new MissionCompositionId($this->firmId(), $programId, $missionId);
        $learningMaterial = $service->showById($missionCompositionId, $learningMaterialId);
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($programId, $missionId)
    {
        $service = $this->buildViewService();
        $missionCompositionId = new MissionCompositionId($this->firmId(), $programId, $missionId);
        $learningMaterials = $service->showAll($missionCompositionId, $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($learningMaterials);
    }
    
    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
        ];
    }
    
    protected function buildAddService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        
        return new LearningMaterialAdd($learningMaterialRepository, $missionRepository);
    }
    protected function buildUpdateService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new LearningMaterialUpdate($learningMaterialRepository);
    }
    protected function buildRemoveService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new LearningMaterialRemove($learningMaterialRepository);
    }
    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new LearningMaterialView($learningMaterialRepository);
    }

}
