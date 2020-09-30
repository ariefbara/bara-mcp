<?php

namespace App\Http\Controllers\Client\TeamMembership;

use Query\Domain\ {
    Model\Firm\Team\TeamProgramParticipation,
    Service\Firm\Team\TeamProgramParticipationFinder
};

class TeamProgramParticipationBaseController extends TeamMembershipBaseController
{
    protected function teamProgramParticipationFinder(): TeamProgramParticipationFinder
    {
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new TeamProgramParticipationFinder($teamProgramParticipationRepository);
    }
}
