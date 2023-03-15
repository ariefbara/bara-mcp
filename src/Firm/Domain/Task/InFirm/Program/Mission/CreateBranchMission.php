<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ManagerTaskInFirm;
use Firm\Domain\Task\Dependency\Firm\Program\MissionRepository;

class CreateBranchMission implements ManagerTaskInFirm
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
     * @param Firm $firm
     * @param CreateBranchMissionPayload $payload
     * @return void
     */
    public function executeInFirm(Firm $firm, $payload): void
    {
        $missionData = $payload->getMissionData();
        $missionData->id = $this->missionRepository->nextIdentity();
        
        $parentMission = $this->missionRepository->aMissionOfId($payload->getParentMissionId());
        $parentMission->assertManageableInFirm($firm);
        
        $branchMission = $parentMission->createBranch($missionData->id, $missionData);
        $this->missionRepository->add($branchMission);
    }

}
