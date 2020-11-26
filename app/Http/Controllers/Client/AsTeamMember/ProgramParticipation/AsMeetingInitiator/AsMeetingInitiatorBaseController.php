<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\ {
    Application\Auth\AuthorizeTeamIsActiveMeetingInitiator,
    Domain\Model\Firm\Program\Activity\Invitee
};

class AsMeetingInitiatorBaseController extends AsTeamMemberBaseController
{

    protected function authorizeTeamIsMeetingInitiator($teamId, $meetingId)
    {
        $meetingAttendeeRepository = $this->em->getRepository(Invitee::class);
        $authZ = new AuthorizeTeamIsActiveMeetingInitiator($meetingAttendeeRepository);
        $authZ->execute($this->firmId(), $teamId, $meetingId);
    }

}
