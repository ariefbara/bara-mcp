<?php

namespace ActivityInvitee\Application\Service\TeamMember;

use ActivityInvitee\Domain\DependencyModel\Firm\Client\TeamMembership;

interface TeamMemberRepository
{
    public function aTeamMembershipCorrespondWithTeam(string $firmId, string $clientId, string $teamId): TeamMembership;
}
