<?php

namespace Firm\Domain\Model\Firm;

class TeamData
{

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var MemberData[]
     */
    protected $memberDataList;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getMemberDataList(): array
    {
        return $this->memberDataList;
    }

    public function __construct(?string $name)
    {
        $this->name = $name;
        $this->memberDataList = [];
    }

    public function addMemberData(Team\MemberData $memberData): void
    {
        $this->memberDataList[] = $memberData;
    }

}
