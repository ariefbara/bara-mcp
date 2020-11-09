<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitation;

class ViewInvitationForClientParticipant
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
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return ParticipantInvitation[]
     */
    public function showAll(string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize)
    {
        return $this->participantInvitationRepository->allInvitationsForClientParticipant(
                        $firmId, $clientId, $programParticipationId, $page, $pageSize);
    }

    public function showById(string $firmId, string $clientId, string $invitationId): ParticipantInvitation
    {
        return $this->participantInvitationRepository->anInvitationForClient($firmId, $clientId, $invitationId);
    }

}