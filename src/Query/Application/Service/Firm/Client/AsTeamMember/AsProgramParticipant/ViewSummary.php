<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\Application\Service\Firm\Client\AsTeamMember\TeamMemberRepository;
use Query\Domain\Service\DataFinder;

class ViewSummary
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamProgramParticipationRepository
     */
    protected $teamProgramParticipationRepository;
    
    /**
     * 
     * @var DataFinder
     */
    protected $dataFinder;
    
    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository, DataFinder $dataFinder)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->dataFinder = $dataFinder;
    }
    
    public function execute($clientId, $teamId, $programParticipationId): array
    {
        $teamProgramParticipation = $this->teamProgramParticipationRepository->ofId($programParticipationId);
        return $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($clientId, $teamId)
                ->viewSummaryOfProgramParticipation($teamProgramParticipation, $this->dataFinder);
        
    }


}
