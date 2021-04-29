<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Firm\Domain\Model\Firm\Program\TeamParticipant;
use Query\Application\Auth\Firm\Program\TeamParticipantAuthorization;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

class AsProgramParticipantBaseController extends AsTeamMemberBaseController
{
    protected function authorizedTeamIsActiveParticipantOfProgram($teamId, $programId): void
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $authZ = new TeamParticipantAuthorization($participantRepository);
        $authZ->execute($teamId, $programId);
    }
    protected function teamParticipantQueryRepository()
    {
        return $this->em->getRepository(TeamProgramParticipation::class);
    }
    protected function teamParticipantFirmRepository()
    {
        return $this->em->getRepository(TeamParticipant::class);
    }
}
