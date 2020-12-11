<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramRegistrant;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\Application\Auth\AuthorizeTeamIsUnconcludedProgramRegistrant;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration;

class AsProgramRegistrantBaseController extends AsTeamMemberBaseController
{
    protected function authorizeTeamIsUnconcludedProgramRegistrant($teamId, $programId): void
    {
        $teamRegistrantRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $authZ = new AuthorizeTeamIsUnconcludedProgramRegistrant($teamRegistrantRepository);
        $authZ->execute($this->firmId(), $teamId, $programId);
    }
}
