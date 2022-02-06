<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant\Mission;

use App\Http\Controllers\Personnel\AsProgramConsultant\AsProgramConsultantBaseController;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

class LearningMaterialController extends AsProgramConsultantBaseController
{
    public function show($programId, $missionId, $learningMaterialId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $learningMaterial = $this->buildViewService()
                ->showById($this->firmId(), $programId, $missionId, $learningMaterialId);
        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($learningMaterial));
    }
    public function showAll($programId, $missionId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $learningMaterials = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $missionId, $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($learningMaterials);
    }
    
    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        $learningAttachments = [];
        foreach ($learningMaterial->iterateAllActiveLearningAttachments() as $learningAttachment) {
            $learningAttachments[] = [
                'id' => $learningAttachment->getId(),
                'firmFileInfo' => [
                    'id' => $learningAttachment->getFirmFileInfo()->getId(),
                    'path' => $learningAttachment->getFirmFileInfo()->getFullyQualifiedFileName(),
                ],
            ];
        }
        return [
            "id" => $learningMaterial->getId(),
            "name" => $learningMaterial->getName(),
            "content" => $learningMaterial->getContent(),
            'learningAttachments' => $learningAttachments,
        ];
    }

    protected function buildViewService()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        return new ViewLearningMaterial($learningMaterialRepository);
    }
}
