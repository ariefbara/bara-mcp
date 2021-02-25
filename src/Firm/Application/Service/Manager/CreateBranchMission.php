<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\MissionData;

class CreateBranchMission
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

    /**
     * 
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;

    public function __construct(ManagerRepository $managerRepository, MissionRepository $missionRepository,
            WorksheetFormRepository $worksheetFormRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->missionRepository = $missionRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $parentMissionId, string $worksheetFormId,
            MissionData $missionData): string
    {
        $id = $this->missionRepository->nextIdentity();
        $parentMission = $this->missionRepository->aMissionOfId($parentMissionId);
        $worksheetForm = $this->worksheetFormRepository->aWorksheetFormOfId($worksheetFormId);
        $mission = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createBranchMission($id, $parentMission, $worksheetForm, $missionData);
        $this->missionRepository->add($mission);
        return $id;
    }

}
