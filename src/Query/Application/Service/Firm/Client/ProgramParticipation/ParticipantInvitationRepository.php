<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ParticipantInvitationRepository
{

    public function anInvitationForClient(string $firmId, string $clientId, string $invitationId): ParticipantInvitee;

    public function allInvitationsForClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter);
}
