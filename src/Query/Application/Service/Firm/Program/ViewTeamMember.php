<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Team\Member;

class ViewTeamMember
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    function __construct(TeamMemberRepository $teamMemberRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $programId
     * @param string $teamId
     * @param int $page
     * @param int $pageSize
     * @param bool|null $activeStatus
     * @return Member[]
     */
    public function showAll(string $firmId, string $teamId, int $page, int $pageSize, ?bool $activeStatus)
    {
        return $this->teamMemberRepository->allMembersOfTeam($firmId, $teamId, $page, $pageSize, $activeStatus);
    }

    public function showById(string $firmId, string $teamMemberId): Member
    {
        return $this->teamMemberRepository->aTeamMemberInFirm($firmId, $teamMemberId);
    }

}
