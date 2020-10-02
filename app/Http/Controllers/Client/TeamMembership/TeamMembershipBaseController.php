<?php

namespace App\Http\Controllers\Client\TeamMembership;

use App\Http\Controllers\Client\ClientBaseController;
use Query\ {
    Application\Auth\Firm\Client\TeamMembershipAuthorization,
    Application\Service\Firm\Client\TeamMembership\ActiveTeamMembershipAuthorization,
    Domain\Model\Firm\Team\Member
};

class TeamMembershipBaseController extends ClientBaseController
{
    protected function teamMembershipRepository()
    {
        return $this->em->getRepository(Member::class);
    }
    protected function buildActiveTeamMembershipAuthorization(): ActiveTeamMembershipAuthorization
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        return new ActiveTeamMembershipAuthorization($teamMemberRepository);
    }
    protected function authorizeActiveTeamMember(string $teamMembershipId): void
    {
        $teamMembershipRepository = $this->em->getRepository(Member::class);
        $authZ = new TeamMembershipAuthorization($teamMembershipRepository);
        $authZ->execute($this->firmId(), $this->clientId(), $teamMembershipId);
    }
}
