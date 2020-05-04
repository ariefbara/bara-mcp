<?php

namespace Firm\Application\Service\Firm\Program;

class MissionPublish
{
    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;
    
    function __construct(MissionRepository $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }
    public function execute(ProgramCompositionId $programCompositionId, string $missionId): void
    {
        $mission = $this->missionRepository->ofId($programCompositionId, $missionId);
        $mission->publish();
        $this->missionRepository->update();
    }
}
