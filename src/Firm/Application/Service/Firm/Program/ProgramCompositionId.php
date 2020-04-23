<?php

namespace Firm\Application\Service\Firm\Program;

class ProgramCompositionId
{

    protected $firmId, $programId;

    function getFirmId()
    {
        return $this->firmId;
    }

    function getProgramId()
    {
        return $this->programId;
    }

    function __construct($firmId, $programId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
    }

}
