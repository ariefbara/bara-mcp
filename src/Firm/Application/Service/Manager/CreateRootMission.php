<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\MissionData;

class CreateRootMission
{

    /**
     * 
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var ProgramRepository
     */
    protected $programRepository;

    /**
     * 
     * @var WorksheetFormRepository
     */
    protected $worksheetFormRepository;

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    public function __construct(
            ManagerRepository $managerRepository, ProgramRepository $programRepository,
            WorksheetFormRepository $worksheetFormRepository, MissionRepository $missionRepository)
    {
        $this->managerRepository = $managerRepository;
        $this->programRepository = $programRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            string $firmId, string $managerId, string $programId, string $worksheetFormId, MissionData $missionData): string
    {
        $id = $this->missionRepository->nextIdentity();
        $program = $this->programRepository->aProgramOfId($programId);
        $worksheetForm = $this->worksheetFormRepository->aWorksheetFormOfId($worksheetFormId);
        $mission = $this->managerRepository->aManagerInFirm($firmId, $managerId)
                ->createRootMission($id, $program, $worksheetForm, $missionData);
        $this->missionRepository->add($mission);
        return $id;
    }

}
