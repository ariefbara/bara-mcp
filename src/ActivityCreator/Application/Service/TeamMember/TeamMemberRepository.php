<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\Domain\DependencyModel\Firm\Client\TeamMembership;

interface TeamMemberRepository
{
    public function aTeamMembershipCorrespondWithTeam(string $firmId, string $clientId, string $teamId): TeamMembership;
}
