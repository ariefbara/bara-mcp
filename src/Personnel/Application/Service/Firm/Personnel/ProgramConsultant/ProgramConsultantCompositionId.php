<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

class ProgramConsultantCompositionId
{

    protected $firmId, $personnelId, $programConsultantId;

    function getFirmId()
    {
        return $this->firmId;
    }

    function getPersonnelId()
    {
        return $this->personnelId;
    }

    function getProgramConsultantId()
    {
        return $this->programConsultantId;
    }

    function __construct($firmId, $personnelId, $programConsultantId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->programConsultantId = $programConsultantId;
    }

}
