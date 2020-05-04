<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\{
    Application\Service\Firm\Program\MissionRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Mission\LearningMaterial
};

class LearningMaterialAdd
{

    /**
     *
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(LearningMaterialRepository $learningMaterialRepository, MissionRepository $missionRepository)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(ProgramCompositionId $programCompositionId, string $missionId, string $name, string $content): string
    {
        $mission = $this->missionRepository->ofId($programCompositionId, $missionId);
        $id = $this->learningMaterialRepository->nextIdentity();

        $learningMaterial = new LearningMaterial($mission, $id, $name, $content);
        $this->learningMaterialRepository->add($learningMaterial);
        return $id;
    }

}
