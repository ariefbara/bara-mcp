<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

class LearningMaterialView
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
    
    public function showById(MissionCompositionId $missionCompositionId, string $learningMaterialId): LearningMaterial
    {
        return $this->learningMaterialRepository->ofId($missionCompositionId, $learningMaterialId);
    }
    
    /**
     * 
     * @param MissionCompositionId $missionCompositionId
     * @param int $page
     * @param int $pageSize
     * @return LearningMaterial[]
     */
    public function showAll(MissionCompositionId $missionCompositionId, int $page, int $pageSize)
    {
        return $this->learningMaterialRepository->all($missionCompositionId, $page, $pageSize);
    }

}
