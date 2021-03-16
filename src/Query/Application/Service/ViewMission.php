<?php

namespace Query\Application\Service;

use Query\Domain\Model\Firm\Program\Mission;

class ViewMission
{

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(MissionRepository $missionRepository)
    {
        $this->missionRepository = $missionRepository;
    }

    /**
     * 
     * @param string $programId
     * @return Mission[]
     */
    public function showAll(string $programId)
    {
        return $this->missionRepository->allPublishedMissionInProgram($programId);
    }

    public function showById(string $id): Mission
    {
        return $this->missionRepository->aPublishedMission($id);
    }

}
