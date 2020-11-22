<?php

namespace App\Http\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use App\Http\Controllers\Client\ClientBaseController;
use Query\{
    Application\Auth\AuthorizeClientIsActiveMeetingInitiator,
    Domain\Model\Firm\Program\Activity\Invitee
};

class AsMeetingInitiatorBaseController extends ClientBaseController
{

    protected function authorizeClientIsMeetingInitiator($meetingId)
    {
        $meetingAttendeeRepository = $this->em->getRepository(Invitee::class);
        $authZ = new AuthorizeClientIsActiveMeetingInitiator($meetingAttendeeRepository);
        $authZ->execute($this->firmId(), $this->clientId(), $meetingId);
    }

}
