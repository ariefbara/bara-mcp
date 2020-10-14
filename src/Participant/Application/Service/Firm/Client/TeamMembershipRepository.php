<?php

namespace Participant\Application\Service\Firm\Client;

use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;

interface TeamMembershipRepository
{
    public function ofId(string $firmId, string $clientId, string $teamMembershipId): TeamMembership;
    
    public function aTeamMembershipById(string $teamMembershipId): TeamMembership;
}
