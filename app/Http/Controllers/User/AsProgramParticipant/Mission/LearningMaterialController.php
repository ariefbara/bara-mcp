<?php

namespace App\Http\Controllers\User\AsProgramParticipant\Mission;

use App\Http\Controllers\User\AsProgramParticipant\AsProgramParticipantBaseController;
use Query\ {
    Application\Service\Firm\Program\Mission\ViewLearningMaterial,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $missionId, $learningMaterialId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $learningMaterial = $service->showById($firmId, $programId, $missionId, $learningMaterialId);
        
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($firmId, $programId, $missionId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $learningMaterials = $service->showAll($firmId, $programId, $missionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($learningMaterials);
        foreach ($learningMaterials as $learningMaterial) {
            $result['list'][] = $this->arrayDataOfLearningMaterial($learningMaterial);
        }
        return $this->listQueryResponse($result);
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
