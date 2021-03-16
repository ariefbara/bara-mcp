<?php

namespace Firm\Application\Service\Manager;

class PublishMission
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
    
    public function execute(string $firmId, string $managerId, string $missionId): void
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->publishMission($mission);
        $this->managerRepository->update();
    }

}
