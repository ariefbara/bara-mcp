<?php

namespace Firm\Application\Service\Firm\Program\Mission;

class LearningMaterialRemove
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
    
    public function execute(MissionCompositionId $missionCompositionId, string $learningMaterialId): void
    {
        $this->learningMaterialRepository->ofId($missionCompositionId, $learningMaterialId)->remove();
        $this->learningMaterialRepository->update();
    }

}
