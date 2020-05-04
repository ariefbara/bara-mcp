<?php

namespace Query\Application\Service\Firm\Program\Participant;

class ParticipantCompositionId
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

    function __construct(string $firmId, string $programId, string $participantId)
    {
        $this->firmId = $firmId;
        $this->programId = $programId;
        $this->participantId = $participantId;
    }

}
