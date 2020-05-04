<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

class WorksheetCompositionId
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $participantId;

    /**
     *
     * @var string
     */
    protected $worksheetId;

    function getFirmId(): string
    {
        return $this->firmId;
    }

    function getProgramId(): string
    {
        return $this->programId;
    }

    function getParticipantId(): string
    {
        return $this->participantId;
    }

    function getWorksheetId(): string
    {
        return $this->worksheetId;
    }

    function __construct(string $firmId, string $programId, string $participantId, string $worksheetId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->participantId = $participantId;
        $this->worksheetId = $worksheetId;
    }

}
