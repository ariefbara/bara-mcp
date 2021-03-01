<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

class ViewOKRPeriod
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    /**
     * 
     * @var OKRPeriodRepository
     */
    protected $okrPeriodRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository, OKRPeriodRepository $okrPeriodRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->okrPeriodRepository = $okrPeriodRepository;
    }
    
    public function showAll(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, int $page, int $pageSize)
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->viewAllOKRPeriodsOfTeamParticipant($this->okrPeriodRepository, $teamParticipant, $page, $pageSize);
    }

    public function showById(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, string $okrPeriodId): OKRPeriod
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewOKRPeriodOfTeamParticipant($this->okrPeriodRepository, $teamParticipant, $okrPeriodId);
    }

}
