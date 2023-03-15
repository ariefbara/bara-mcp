<?php

namespace Firm\Domain\Task\InFirm\Program\Mission;

class AssignWorksheetFormToMissionPayload
{

    /**
     * 
     * @var string
     */
    protected $missionId;

    /**
     * 
     * @var string
     */
    protected $worksheetFormId;

    public function getMissionId(): string
    {
        return $this->missionId;
    }

    public function getWorksheetFormId(): string
    {
        return $this->worksheetFormId;
    }

    public function setMissionId(string $missionId)
    {
        $this->missionId = $missionId;
        return $this;
    }

    public function setWorksheetFormId(string $worksheetFormId)
    {
        $this->worksheetFormId = $worksheetFormId;
        return $this;
    }

}
