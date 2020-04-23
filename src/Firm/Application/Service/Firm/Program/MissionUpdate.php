<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\Domain\Model\Firm\Program\Mission;

class MissionUpdate
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

    public function execute(ProgramCompositionId $programCompositionId, $missionId, $name, $description, $position): Mission
    {
        $mission = $this->missionRepository->ofId($programCompositionId, $missionId);
        $mission->update($name, $description, $position);
        $this->missionRepository->update();
        return $mission;
    }

}
