<?php

namespace Firm\Application\Service\Firm\Program\Mission;

class MissionCompositionId
{

    protected $firmId, $programId, $missionId;

    function getFirmId()
    {
        return $this->firmId;
    }

    function getProgramId()
    {
        return $this->programId;
    }

    function getMissionId()
    {
        return $this->missionId;
    }

    function __construct($firmId, $programId, $missionId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->missionId = $missionId;
    }

}
