<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitation;

interface ParticipantInvitationRepository
{

    public function anInvitationForClient(string $firmId, string $clientId, string $invitationId): ParticipantInvitation;

    public function allInvitationsForClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize);
}
