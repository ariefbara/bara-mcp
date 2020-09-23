<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\TeamMembership;

interface TeamMembershipRepository
{

    public function ofId(string $firmId, string $clientId, string $teamMembershipId): TeamMembership;

    public function update(): void;
}
