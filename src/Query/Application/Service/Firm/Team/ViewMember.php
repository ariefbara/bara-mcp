<?php

namespace Query\Application\Service\Firm\Team;

use Query\Domain\Model\Firm\Team\Member;

class ViewMember
{

    /**
     *
     * @var MemberRepository
     */
    protected $memberRepository;

    public function __construct(MemberRepository $memberRepository)
    {
        $this->memberRepository = $memberRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param int $page
     * @param int $pageSize
     * @return Member[]
     */
    public function showAll(string $firmId, string $teamId, int $page, int $pageSize)
    {
        return $this->memberRepository->all($firmId, $teamId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $memberId): Member
    {
        return $this->memberRepository->ofId($firmId, $teamId, $memberId);
    }

}
