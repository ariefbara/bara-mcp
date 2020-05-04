<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Mission;

class MissionView
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

    public function showById(ProgramCompositionId $programCompositionId, string $missionId): Mission
    {
        return $this->missionRepository->ofId($programCompositionId, $missionId);
    }

    /**
     * 
     * @param ProgramCompositionId $programCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Mission[]
     */
    public function showAll(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        return $this->missionRepository->all($programCompositionId, $page, $pageSize);
    }

}
