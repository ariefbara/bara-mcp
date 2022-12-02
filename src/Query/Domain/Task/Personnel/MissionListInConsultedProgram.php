<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\MissionRepository;

class MissionListInConsultedProgram implements PersonnelTask
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
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->missionRepository->missionListInAllProgramConsultedByPersonnel($personnelId);
    }

}
