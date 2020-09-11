<?php

namespace Query\Application\Service\Firm\Program\Mission;

use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

class ViewLearningMaterial
{
    /**
     *
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;
    
    public function __construct(LearningMaterialRepository $learningMaterialRepository)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
    }
    
    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $missionId
     * @param int $page
     * @param int $pageSize
     * @return LearningMaterial[]
     */
    public function showAll(string $firmId, string $programId, string $missionId, int $page, int $pageSize)
    {
        return $this->learningMaterialRepository->all($firmId, $programId, $missionId, $page, $pageSize);
    }
    
    public function showById(string $firmId, string $programId, string $missionId, string $learningMaterialId): LearningMaterial
    {
        return $this->learningMaterialRepository->ofId($firmId, $programId, $missionId, $learningMaterialId);
    }

}
