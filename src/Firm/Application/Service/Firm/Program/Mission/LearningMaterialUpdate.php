<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

class LearningMaterialUpdate
{
    /**
     *
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;
    
    function __construct(LearningMaterialRepository $learningMaterialRepository)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
    }
    
    public function execute(
            MissionCompositionId $missionCompositionId, string $learningMaterialId, string $name, string $content): void
    {
        $learningMaterial = $this->learningMaterialRepository->ofId($missionCompositionId, $learningMaterialId);
        $learningMaterial->update($name, $content);
        $this->learningMaterialRepository->update();
    }

}
