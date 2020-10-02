<?php

namespace Query\Application\Auth\Firm\Client;

interface TeamMembershipRepository
{
    public function containRecordOfActiveTeamMembership(string $firmId, string $clientId, string $teamMembershipId): bool;
}
