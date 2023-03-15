<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ManagerTaskInFirm;
use Firm\Domain\Task\Dependency\Firm\Program\MissionRepository;
use Firm\Domain\Task\Dependency\Firm\ProgramRepository;

class CreateRootMission implements ManagerTaskInFirm
{

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    public function __construct(MissionRepository $missionRepository, ProgramRepository $programRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * 
     * @param Firm $firm
     * @param CreateRootMissionPayload $payload
     * @return void
     */
    public function executeInFirm(Firm $firm, $payload): void
    {
        $missionData = $payload->getMissionData();
        $missionData->id = $this->missionRepository->nextIdentity();
        
        $program = $this->programRepository->aProgramOfId($payload->getProgramId());
        $program->assertManageableInFirm($firm);
        
        $mission = $program->createRootMission($missionData->id, $missionData);
        $this->missionRepository->add($mission);
    }

}
