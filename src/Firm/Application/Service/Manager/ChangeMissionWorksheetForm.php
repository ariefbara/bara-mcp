<?php

namespace Firm\Application\Service\Manager;

class ChangeMissionWorksheetForm
{
    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;
    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;
    /**
     * 
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;
    
    function __construct(MissionRepository $missionRepository, ManagerRepository $managerRepository,
            WorksheetFormRepository $worksheetFormRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->managerRepository = $managerRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }
    
    public function execute(string $firmId, string $managerId, string $missionId, string $worksheetFormId): void
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $worksheetForm = $this->worksheetFormRepository->aWorksheetFormOfId($worksheetFormId);
        
        $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->changeMissionsWorksheetForm($mission, $worksheetForm);
        
        $this->missionRepository->update();
    }

}
