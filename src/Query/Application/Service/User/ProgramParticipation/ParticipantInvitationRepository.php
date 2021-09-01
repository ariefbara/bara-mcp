<?php

namespace Query\Application\Service\User\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ParticipantInvitationRepository
{

    public function anInvitationForUser(string $userId, string $invitationId): ParticipantInvitee;

    public function allInvitationsForUserParticipant(
            string $userId, string $programParticipationId, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter);
}
