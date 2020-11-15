<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;

interface ParticipantInvitationRepository
{

    public function anInvitationForTeam(string $firmId, string $teamId, string $invitationId): ParticipantInvitee;

    public function allInvitationsForTeamParticipant(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize);
}
