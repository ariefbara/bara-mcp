<?php

namespace Firm\Domain\Task\InFirm;

class AddTeamMemberPayload
{

    /**
     * 
     * @var string|null
     */
    protected $teamId;

    /**
     * 
     * @var MemberDataRequest|null
     */
    protected $memberDataRequest;

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function __construct(?string $teamId, ?MemberDataRequest $memberDataRequest)
    {
        $this->teamId = $teamId;
        $this->memberDataRequest = $memberDataRequest;
    }

    public function getClientId(): ?string
    {
        return $this->memberDataRequest->getClientId();
    }

    public function getPosition(): ?string
    {
        return $this->memberDataRequest->getPosition();
    }

}
