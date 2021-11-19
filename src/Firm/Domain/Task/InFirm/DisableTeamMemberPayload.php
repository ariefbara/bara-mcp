<?php

namespace Firm\Domain\Task\InFirm;

class DisableTeamMemberPayload
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
    protected $memberId;

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function __construct(?string $teamId, ?string $memberId)
    {
        $this->teamId = $teamId;
        $this->memberId = $memberId;
    }

}
