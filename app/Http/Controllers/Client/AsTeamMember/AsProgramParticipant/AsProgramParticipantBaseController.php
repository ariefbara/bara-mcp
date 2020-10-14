<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\ {
    Application\Auth\Firm\Program\TeamParticipantAuthorization,
    Domain\Model\Firm\Program\Participant
};

class AsProgramParticipantBaseController extends AsTeamMemberBaseController
{
    protected function authorizedTeamIsActiveProgramParticipant($teamId, $programId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new TeamParticipantAuthorization($participantRepository);
        $authZ->execute($teamId, $programId);
    }
}
