<?php

namespace Team\Application\Service\Firm\Client;

use Team\Domain\Model\Team\Member;

interface TeamMembershipRepository
{
    public function aTeamMembershipOfClient(string $firmId, string $clientId, string $teamMembershipId): Member;
}
