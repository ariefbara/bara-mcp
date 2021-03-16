<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember;

use Query\Domain\Model\Firm\Team\Member\TeamMemberActivityLog;

class ViewTeamMemberActivityLog
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamMemberActivityLogRepository
     */
    protected $teamMemberActivityLogRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamMemberActivityLogRepository $teamMemberActivityLogRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamMemberActivityLogRepository = $teamMemberActivityLogRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $memberId
     * @param int $page
     * @param int $pageSize
     * @return TeamMemberActivityLog[]
     */
    public function showAll(string $firmId, string $clientId, string $memberId, int $page, int $pageSize)
    {
        return $this->teamMemberRepository->aTeamMembershipOfClient($firmId, $clientId, $memberId)
                        ->viewAllSelfActivityLogs($this->teamMemberActivityLogRepository, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $memberId, string $teamMemberActivityLogId): TeamMemberActivityLog
    {
        return $this->teamMemberRepository->aTeamMembershipOfClient($firmId, $clientId, $memberId)
                        ->viewSelfActivityLog($this->teamMemberActivityLogRepository, $teamMemberActivityLogId);
    }

}
