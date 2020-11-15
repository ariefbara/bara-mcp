<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;

class ViewInvitationForUserParticipant
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
     * @param string $userId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantInvitee[]
     */
    public function showAll(string $userId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantInvitationRepository->allInvitationsForUserParticipant(
                        $userId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $userId, string $invitationId): ParticipantInvitee
    {
        return $this->participantInvitationRepository->anInvitationForUser($userId, $invitationId);
    }

}
