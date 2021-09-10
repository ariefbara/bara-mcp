<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator\Mission;

use App\Http\Controllers\Personnel\AsProgramCoordinator\AsProgramCoordinatorBaseController;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

class LearningMaterialController extends AsProgramCoordinatorBaseController
{
    public function show($programId, $missionId, $learningMaterialId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $learningMaterial = $this->buildViewService()
                ->showById($this->firmId(), $programId, $missionId, $learningMaterialId);
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }
    public function showAll($programId, $missionId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $learningMaterials = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $missionId, $this->getPage(), $this->getPageSize());
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

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }
}
