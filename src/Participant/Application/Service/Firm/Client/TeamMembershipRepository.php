<?php

namespace Participant\Application\Service\Firm\Client;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;

interface TeamMembershipRepository
{
    public function aTeamMembershipCorrespondWithTeam(string $firmId, string $clientId, string $teamId): TeamMembership;
    
    public function aTeamMembershipById(string $teamMembershipId): TeamMembership;
}
