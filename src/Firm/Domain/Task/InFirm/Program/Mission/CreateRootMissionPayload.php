<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

use Firm\Domain\Model\Firm\Program\MissionData;

class CreateRootMissionPayload
{

    /**
     * 
     * @var string
     */
    protected $programId;

    /**
     * 
     * @var MissionData
     */
    protected $missionData;

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getMissionData(): MissionData
    {
        return $this->missionData;
    }

    public function setProgramId(string $programId)
    {
        $this->programId = $programId;
        return $this;
    }

    public function setMissionData(MissionData $missionData)
    {
        $this->missionData = $missionData;
        return $this;
    }

}
