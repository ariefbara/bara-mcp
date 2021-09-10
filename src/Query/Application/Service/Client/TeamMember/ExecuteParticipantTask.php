<?php

namespace Query\Application\Service\Client\TeamMember;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;

class ExecuteParticipantTask
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

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId,
            ITaskExecutableByParticipant $task): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                ->executeTeamParticipantTask($teamParticipant, $task);
    }

}
