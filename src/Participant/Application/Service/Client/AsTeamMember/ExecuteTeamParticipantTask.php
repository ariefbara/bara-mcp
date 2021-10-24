<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Model\ITaskExecutableByParticipant;

class ExecuteTeamParticipantTask
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
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->executeTeamParticipantTask($teamParticipant, $task);
        $this->teamMemberRepository->update();
    }

}
