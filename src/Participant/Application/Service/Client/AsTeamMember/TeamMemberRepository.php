<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;

interface TeamMemberRepository
{
    public function aTeamMembershipCorrespondWithTeam(string $firmId, string $clientId, string $teamId): TeamMembership;
}
