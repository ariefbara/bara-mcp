<?php

namespace Firm\Domain\Task\InFirm;

class AddTeamPayload
{

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var MemberDataRequest[]
     */
    protected $memberDataRequestList;

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * 
     * @return MemberDataRequest[]
     */
    public function getMemberDataRequestList(): array
    {
        return $this->memberDataRequestList;
    }

    public function __construct(?string $name)
    {
        $this->name = $name;
        $this->memberDataRequestList = [];
    }

    public function addMemberDataRequest(MemberDataRequest $memberDataRequest): void
    {
        $this->memberDataRequestList[] = $memberDataRequest;
    }

}
