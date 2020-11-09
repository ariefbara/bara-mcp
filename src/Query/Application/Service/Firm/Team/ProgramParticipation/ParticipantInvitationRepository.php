<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitation;

interface ParticipantInvitationRepository
{

    public function anInvitationForTeam(string $firmId, string $teamId, string $invitationId): ParticipantInvitation;

    public function allInvitationsForTeamParticipant(
            string $firmId, string $teamId, string $programParticipationId, int $page, int $pageSize);
}
