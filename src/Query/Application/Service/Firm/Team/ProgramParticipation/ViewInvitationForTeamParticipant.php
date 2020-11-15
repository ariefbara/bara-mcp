<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;

class ViewInvitationForTeamParticipant
{

    /**
     *
     * @var ParticipantInvitationRepository
     */
    protected $participantInvitationRepository;

    function __construct(ParticipantInvitationRepository $participantInvitationRepository)
    {
        $this->participantInvitationRepository = $participantInvitationRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $teamId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantInvitee[]
     */
    public function showAll(string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantInvitationRepository->allInvitationsForTeamParticipant(
                        $firmId, $teamId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $firmId, string $teamId, string $invitationId): ParticipantInvitee
    {
        return $this->participantInvitationRepository->anInvitationForTeam($firmId, $teamId, $invitationId);
    }

}
