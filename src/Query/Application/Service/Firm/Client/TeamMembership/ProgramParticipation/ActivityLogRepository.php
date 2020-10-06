<?php

namespace Query\Application\Service\Firm\Client\TeamMembership\ProgramParticipation;

interface ActivityLogRepository
{
    public function allActivityLogsBelongsToTeamParticipantWhereClientIsMember(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId, int $page, int $pageSize);
}
