<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember;

use Query\Domain\Service\DataFinder;
use Query\Domain\Service\Firm\Team\TeamProgramParticipationRepository;

class ViewAllActiveProgramParticipationSummary
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var DataFinder
     */
    protected $dataFinder;

    public function __construct(TeamMemberRepository $teamMemberRepository, DataFinder $dataFinder)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->dataFinder = $dataFinder;
    }

    public function execute(string $clientId, string $teamId, int $page, int $pageSize): array
    {
        return $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($clientId, $teamId)
                ->viewAllActiveProgramParticipationSummary($this->dataFinder, $page, $pageSize);
    }

}
