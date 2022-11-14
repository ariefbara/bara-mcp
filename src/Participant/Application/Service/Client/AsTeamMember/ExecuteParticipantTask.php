<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\Task\Participant\ParticipantTask;

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

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, ParticipantTask $task, $payload): void
    {
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->executeParticipantTask($teamParticipant, $task, $payload);
        $this->teamMemberRepository->update();
    }

}
