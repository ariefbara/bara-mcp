<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ManagerTaskInFirm;
use Firm\Domain\Task\Dependency\Firm\Program\MissionRepository;
use Firm\Domain\Task\Dependency\Firm\WorksheetFormRepository;

class AssignWorksheetFormToMission implements ManagerTaskInFirm
{

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

    public function __construct(MissionRepository $missionRepository, WorksheetFormRepository $worksheetFormRepository)
    {
        $this->missionRepository = $missionRepository;
        $this->worksheetFormRepository = $worksheetFormRepository;
    }

    /**
     * 
     * @param Firm $firm
     * @param AssignWorksheetFormToMissionPayload $payload
     * @return void
     */
    public function executeInFirm(Firm $firm, $payload): void
    {
        $mission = $this->missionRepository->aMissionOfId($payload->getMissionId());
        $mission->assertManageableInFirm($firm);
        
        $worksheetForm = $this->worksheetFormRepository->aWorksheetFormOfId($payload->getWorksheetFormId());
        $worksheetForm->assertAccessibleInFirm($firm);
        
        $mission->assignWorksheetForm($worksheetForm);
    }

}
