<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\MissionData;

class UpdateMission
{
    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;
    
    public function __construct(ManagerRepository $managerRepository, MissionRepository $missionRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->missionRepository = $missionRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $missionId, MissionData $missionData): void
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->updateMission($mission, $missionData);
        $this->managerRepository->update();
    }

}
