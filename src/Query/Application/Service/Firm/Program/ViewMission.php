<?php

namespace Query\Application\Service\Firm\Program;

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
     * @param string $firmId
     * @param string $programId
     * @param int $page
     * @param int $pageSize
     * @param string|null $position
     * @return Mission[]
     */
    public function showAll(string $firmId, string $programId, int $page, int $pageSize, ?bool $publishedOnly = true)
    {
        return $this->missionRepository->all($firmId, $programId, $page, $pageSize, $publishedOnly);
    }

    public function showById(string $firmId, string $programId, string $missionId): Mission
    {
        return $this->missionRepository->ofId($firmId, $programId, $missionId);
    }
    
    public function showByPosition(string $programId, string $position): Mission
    {
        return $this->missionRepository->aMissionByPositionBelongsToProgram($programId, $position);
    }

}
