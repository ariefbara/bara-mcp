<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm\Program\MissionData;

class CreateBranchMissionPayload
{

    public $id;

    /**
     * 
     * @var MissionData
     */
    protected $missionData;

    /**
     * 
     * @var string
     */
    protected $parentMissionId;

    public function getMissionData(): MissionData
    {
        return $this->missionData;
    }

    public function getParentMissionId(): string
    {
        return $this->parentMissionId;
    }

    public function setMissionData(MissionData $missionData)
    {
        $this->missionData = $missionData;
        return $this;
    }

    public function setParentMissionId(string $parentMissionId)
    {
        $this->parentMissionId = $parentMissionId;
        return $this;
    }

}
