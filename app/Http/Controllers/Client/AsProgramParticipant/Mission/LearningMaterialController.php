<?php

namespace App\Http\Controllers\Client\AsProgramParticipant\Mission;

use App\Http\Controllers\Client\AsProgramParticipant\AsProgramParticipantBaseController;
use Firm\Application\Service\Firm\Program\Mission\MissionCompositionId;
use Query\ {
    Application\Service\Firm\Program\Mission\LearningMaterialView,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};

class LearningMaterialController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $missionId, $learningMaterialId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $missionCompositionId = new MissionCompositionId($firmId, $programId, $missionId);
        $learningMaterial = $service->showById($missionCompositionId, $learningMaterialId);
        
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }

    public function showAll($firmId, $programId, $missionId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $missionCompositionId = new MissionCompositionId($firmId, $programId, $missionId);
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

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new LearningMaterialView($learningMaterialRepository);
    }

}
