<?php

namespace App\Http\Controllers\Client\TeamMembership;

use App\Http\Controllers\Client\ClientBaseController;
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ActiveTeamMembershipAuthorization,
    Domain\Model\Firm\Team\Member
};

class TeamMembershipBaseController extends ClientBaseController
{
    protected function buildActiveTeamMembershipAuthorization(): ActiveTeamMembershipAuthorization
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        return new ActiveTeamMembershipAuthorization($teamMemberRepository);
    }
}
