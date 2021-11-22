<?php

namespace Firm\Domain\Task\InFirm;

class AddTeamParticipantPayload
{

    /**
     * 
     * @var string|null
     */
    protected $teamId;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function getProgramId(): ?string
    {
        return $this->programId;
    }

    public function __construct(?string $teamId, ?string $programId)
    {
        $this->teamId = $teamId;
        $this->programId = $programId;
    }

}
